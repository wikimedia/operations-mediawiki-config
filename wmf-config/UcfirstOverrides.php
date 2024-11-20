<?php
// Use title case, except for:
// 1. Eszett, T292552.
// 2. Overrides to support the migration from PHP 7.4 to 8.1, T372603.
return [
	// T292552: Permanent override for Eszett, which is often used as a page title
	// to describe the character (also used as a stylized B in usernames).
	// Do not remove when cleaning up after the PHP 8.1 migration.
	'ß' => 'ß',
	// T372603: Overrides to maintain title-case consistency between PHP 7.4
	// and 8.1, where mbstring supports Unicode 11.0 and 14.0, respectively.
	// Capital added in 12.0
	'ʂ' => 'ʂ',
	// Capital added in 12.0
	'ᶎ' => 'ᶎ',
	// Added in 14.0
	'ⱟ' => 'ⱟ',
	// Capital added in 12.0
	'ꞔ' => 'ꞔ',
	// Added in 12.0
	'ꞻ' => 'ꞻ',
	// Added in 12.0
	'ꞽ' => 'ꞽ',
	// Added in 12.0
	'ꞿ' => 'ꞿ',
	// Added in 14.0
	'ꟁ' => 'ꟁ',
	// Added in 12.0
	'ꟃ' => 'ꟃ',
	// Added in 13.0
	'ꟈ' => 'ꟈ',
	// Added in 13.0
	'ꟊ' => 'ꟊ',
	// Added in 14.0
	'ꟑ' => 'ꟑ',
	// Added in 14.0
	'ꟗ' => 'ꟗ',
	// Added in 14.0
	'ꟙ' => 'ꟙ',
	// Added in 13.0
	'ꟶ' => 'ꟶ',
	// Added in 14.0
	'𐖗' => '𐖗',
	// Added in 14.0
	'𐖘' => '𐖘',
	// Added in 14.0
	'𐖙' => '𐖙',
	// Added in 14.0
	'𐖚' => '𐖚',
	// Added in 14.0
	'𐖛' => '𐖛',
	// Added in 14.0
	'𐖜' => '𐖜',
	// Added in 14.0
	'𐖝' => '𐖝',
	// Added in 14.0
	'𐖞' => '𐖞',
	// Added in 14.0
	'𐖟' => '𐖟',
	// Added in 14.0
	'𐖠' => '𐖠',
	// Added in 14.0
	'𐖡' => '𐖡',
	// Added in 14.0
	'𐖣' => '𐖣',
	// Added in 14.0
	'𐖤' => '𐖤',
	// Added in 14.0
	'𐖥' => '𐖥',
	// Added in 14.0
	'𐖦' => '𐖦',
	// Added in 14.0
	'𐖧' => '𐖧',
	// Added in 14.0
	'𐖨' => '𐖨',
	// Added in 14.0
	'𐖩' => '𐖩',
	// Added in 14.0
	'𐖪' => '𐖪',
	// Added in 14.0
	'𐖫' => '𐖫',
	// Added in 14.0
	'𐖬' => '𐖬',
	// Added in 14.0
	'𐖭' => '𐖭',
	// Added in 14.0
	'𐖮' => '𐖮',
	// Added in 14.0
	'𐖯' => '𐖯',
	// Added in 14.0
	'𐖰' => '𐖰',
	// Added in 14.0
	'𐖱' => '𐖱',
	// Added in 14.0
	'𐖳' => '𐖳',
	// Added in 14.0
	'𐖴' => '𐖴',
	// Added in 14.0
	'𐖵' => '𐖵',
	// Added in 14.0
	'𐖶' => '𐖶',
	// Added in 14.0
	'𐖷' => '𐖷',
	// Added in 14.0
	'𐖸' => '𐖸',
	// Added in 14.0
	'𐖹' => '𐖹',
	// Added in 14.0
	'𐖻' => '𐖻',
	// Added in 14.0
	'𐖼' => '𐖼',
];
