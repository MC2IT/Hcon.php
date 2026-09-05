<?php declare(strict_types=1);
namespace Mc2it\Hcon;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{DataProvider, Test, TestDox};
use function PHPUnit\Framework\assertArraysAreEqual;

/**
 * Tests the features of the {@see HconSerializer} class.
 */
#[TestDox("HconSerializer")]
final class HconSerializerTests extends TestCase {

	/**
	 * Gets the test data used by the `deserialize()` method.
	 * @return array<array{string, array<string, mixed>}> The test data used by the `deserialize()` method.
	 */
	public static function getDeserializeData(): array {
		return [
			[" ", []],
			['{"foo": 1}', ["foo" => 1]],
			["foo:1 bar:true", ["foo" => 1, "bar" => true]],
			["sse.mode:once", ["sse" => ["mode" => "once"]]],
			["innerHTML, swap:200ms, settle:100ms", ["innerHTML" => true, "swap" => "200ms", "settle" => "100ms"]],
			["click delay:500ms throttle:1s", ["click" => true, "delay" => "500ms", "throttle" => "1s"]],
			['credentials:"include", timeout:5000', ["credentials" => "include", "timeout" => 5000]],
			["token:'abc' retry:3", ["token" => "abc", "retry" => 3]]
		];
	}

	/**
	 * Tests the {@see HconSerializer::deserialize()} method.
	 * @param string $hcon The HCON-formatted string to convert.
	 * @param array<string, mixed> $expected The associative array corresponding to the specified HCON-formatted string.
	 */
	#[Test, TestDox("deserialize()"), DataProvider("getDeserializeData")]
	public function deserialize(string $hcon, array $expected): void {
		assertArraysAreEqual($expected, HconSerializer::deserialize($hcon));
	}
}
