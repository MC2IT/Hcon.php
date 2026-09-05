<?php declare(strict_types=1);
namespace Mc2it\Hcon;

/**
 * Converts a HCON-formatted string to a hash table.
 * @param string $hcon The HCON-formatted string to convert.
 * @param int $depth The maximum depth the HCON input is allowed to have.
 * @return array<string, mixed> The hash table corresponding to the specified HCON-formatted string.
 */
function hcon_decode(string $hcon, int $depth = 1024): array {
	return HconSerializer::deserialize($hcon, $depth);
}
