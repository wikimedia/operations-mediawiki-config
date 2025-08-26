<?php
// Use title case, except for Eszett.
//
// NOTE: When adding or removing title-case overrides for consistency between
// PHP versions during migration, be sure to retain the override for Eszett.
return [
	// T292552: Permanent override for Eszett, which is often used as a page title
	// to describe the character (also used as a stylized B in usernames).
	'ß' => 'ß',
];
