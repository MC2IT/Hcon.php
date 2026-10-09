<?php declare(strict_types=1);
namespace Mc2it\Hcon;

/**
 * Provides functionality to deserialize HCON-formatted string into associative arrays.
 */
class HconSerializer {

	/**
	 * The pattern used to tokenize HCON-formatted strings.
	 */
	private const string HconPattern = '@(?:"([^"]+)"|\'([^\']+)\'|([^\s,:]+))(?:\s*:\s*(?:"([^"]*)"|\'([^\']*)\'|<((?:[^/]|\/(?!>))+)\/>|([^\s,]+)))?(?=\s|,|$)@';

	/**
	 * Converts a HCON-formatted string to an associative array.
	 * @param string $hcon The HCON-formatted string to convert.
	 * @param int $depth The maximum depth the HCON input is allowed to have.
	 * @return array<string, mixed> The associative array corresponding to the specified HCON-formatted string.
	 */
	public static function deserialize(string $hcon, int $depth = 1024): array {
		$depth = max(1, $depth);
		$hcon = trim($hcon);
		if (!mb_strlen($hcon)) return [];
		if (str_starts_with($hcon, "{")) return json_decode($hcon, associative: true, depth: $depth, flags: JSON_THROW_ON_ERROR);
		if (!preg_match_all(self::HconPattern, $hcon, $matches, PREG_SET_ORDER)) return [];

		$target = [];
		foreach ($matches as $match) {
			$doubleQuotedKey = self::getMatchGroup($match, 1); // "key"
			$singleQuotedKey = self::getMatchGroup($match, 2); // 'key'
			$bareKey = (string) self::getMatchGroup($match, 3); // key
			$doubleQuotedValue = self::getMatchGroup($match, 4); // "value"
			$singleQuotedValue = self::getMatchGroup($match, 5); // 'value'
			$hyperscriptValue = self::getMatchGroup($match, 6); // <value/>
			$bareValue = self::getMatchGroup($match, 7); // value

			$key = $doubleQuotedKey ?? $singleQuotedKey ?? $bareKey;
			$value = $doubleQuotedValue ?? $singleQuotedValue ?? $hyperscriptValue ?? $bareValue ?? "true" |> mb_trim(...);
			try { $value = json_decode($value, associative: true, depth: $depth, flags: JSON_THROW_ON_ERROR); } catch (\JsonException) {}

			if (!str_contains($bareKey, ".")) self::mergeArrays([$key => $value], $target);
			else {
				$source = $value;
				$segments = explode(".", $key);
				foreach (range(count($segments) - 1, 0) as $index) $source = [$segments[$index] => $source];
				self::mergeArrays($source, $target);
			}
		}

		return $target;
	}

	/**
	 * Gets the value of the capturing group with the specified index in a given regular expression match.
	 * @param array<string|null> $match The regular expression match.
	 * @param int $index The index of the capturing group.
	 * @return string|null The value of the capturing group, or `null` if the group has not been matched in the input string.
	 */
	private static function getMatchGroup(array $match, int $index): ?string {
		$value = $match[$index] ?? "";
		return mb_strlen($value) ? $value : null;
	}

	/**
	 * Deep-merges a source array into a target array.
	 * @param array<string, mixed> $source The source array.
	 * @param array<string, mixed> $target The target array.
	 */
	private static function mergeArrays(array $source, array &$target): void {
		foreach ($source as $key => $value) {
			if (is_array($value) && is_array($target[$key] ?? null)) self::mergeArrays($value, $target[$key]);
			else $target[$key] = $value;
		}
	}
}
