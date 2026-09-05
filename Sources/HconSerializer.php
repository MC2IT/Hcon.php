<?php declare(strict_types=1);
namespace Mc2it\Hcon;

/**
 * Provides functionality to deserialize HCON-formatted string into associative arrays.
 */
final class HconSerializer {

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
		static $getGroup = function(array $match, int $index): ?string {
			$value = $match[$index] ?? "";
			return mb_strlen($value) ? $value : null;
		};

		$depth = max(1, $depth);
		$hcon = trim($hcon);
		if (!mb_strlen($hcon)) return [];
		if (str_starts_with($hcon, "{")) return json_decode($hcon, associative: true, depth: $depth, flags: JSON_THROW_ON_ERROR);
		if (!preg_match_all(self::HconPattern, $hcon, $matches, PREG_SET_ORDER)) return [];

		$result = [];
		foreach ($matches as $match) {
			$doubleQuotedKey = $getGroup($match, 1); // "key"
			$singleQuotedKey = $getGroup($match, 2); // 'key'
			$bareKey = $getGroup($match, 3); // key
			$doubleQuotedValue = $getGroup($match, 4); // "value"
			$singleQuotedValue = $getGroup($match, 5); // 'value'
			$hyperscriptValue = $getGroup($match, 6); // <value/>
			$bareValue = $getGroup($match, 7); // value

			$key = $doubleQuotedKey ?? $singleQuotedKey ?? $bareKey;
			$value = mb_trim($doubleQuotedValue ?? $singleQuotedValue ?? $hyperscriptValue ?? $bareValue ?? "true");
			try { $value = json_decode($value, associative: true, depth: $depth, flags: JSON_THROW_ON_ERROR); } catch (\JsonException) {}

			if (!str_contains($bareKey, ".")) self::merge([$key => $value], $result);
			else {
				$pair = $value;
				$segments = explode(".", $key);
				for ($index = count($segments) - 1; $index >= 0; $index--) $pair = [$segments[$index] => $pair];
				self::merge($pair, $result);
			}
		}

		return $result;
	}

	/**
	 * Deep-merges a source array into a target array.
	 * @param array<string, mixed> $source The source array.
	 * @param array<string, mixed> $target The target array.
	 */
	private static function merge(array $source, array &$target): void {
		foreach ($source as $key => $value) {
			if (is_array($value) && is_array($target[$key] ?? null)) self::merge($value, $target[$key]);
			else $target[$key] = $value;
		}
	}
}
