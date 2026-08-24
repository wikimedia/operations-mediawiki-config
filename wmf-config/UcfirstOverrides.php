<?php
// Use title case, except for Eszett.
//
// NOTE: When adding or removing title-case overrides for consistency between
// PHP versions during migration, be sure to retain the override for Eszett.
return [
	// T292552: Permanent override for Eszett, which is often used as a page title
	// to describe the character (also used as a stylized B in usernames).
	'ß' => 'ß',
	// T432985: Overrides to maintain title-case consistency between PHP 8.3
	// and 8.5, where mbstring supports Unicode 14.0 and 17.0, respectively.
	// Added in 16.0 -- LATIN CAPITAL LETTER LAMBDA WITH STROKE
	'ƛ' => 'ƛ',
	// Added in 16.0 -- LATIN CAPITAL LETTER RAMS HORN
	'ɤ' => 'ɤ',
	// Added in 16.0 -- CYRILLIC CAPITAL LETTER TJE
	'ᲊ' => 'ᲊ',
	// Added in 16.0 -- LATIN CAPITAL LETTER S WITH DIAGONAL STROKE
	'ꟍ' => 'ꟍ',
	// Added in 17.0 -- LATIN CAPITAL LETTER PHARYNGEAL VOICED FRICATIVE
	'꟏' => '꟏',
	// Added in 17.0 -- LATIN CAPITAL LETTER DOUBLE THORN
	'ꟓ' => 'ꟓ',
	// Added in 17.0 -- LATIN CAPITAL LETTER DOUBLE WYNN
	'ꟕ' => 'ꟕ',
	// Added in 16.0 -- LATIN CAPITAL LETTER LAMBDA
	'ꟛ' => 'ꟛ',
	// Added in 16.0 -- GARAY CAPITAL LETTER A
	'𐵰' => '𐵰',
	// Added in 16.0 -- GARAY CAPITAL LETTER CA
	'𐵱' => '𐵱',
	// Added in 16.0 -- GARAY CAPITAL LETTER MA
	'𐵲' => '𐵲',
	// Added in 16.0 -- GARAY CAPITAL LETTER KA
	'𐵳' => '𐵳',
	// Added in 16.0 -- GARAY CAPITAL LETTER BA
	'𐵴' => '𐵴',
	// Added in 16.0 -- GARAY CAPITAL LETTER JA
	'𐵵' => '𐵵',
	// Added in 16.0 -- GARAY CAPITAL LETTER SA
	'𐵶' => '𐵶',
	// Added in 16.0 -- GARAY CAPITAL LETTER WA
	'𐵷' => '𐵷',
	// Added in 16.0 -- GARAY CAPITAL LETTER LA
	'𐵸' => '𐵸',
	// Added in 16.0 -- GARAY CAPITAL LETTER GA
	'𐵹' => '𐵹',
	// Added in 16.0 -- GARAY CAPITAL LETTER DA
	'𐵺' => '𐵺',
	// Added in 16.0 -- GARAY CAPITAL LETTER XA
	'𐵻' => '𐵻',
	// Added in 16.0 -- GARAY CAPITAL LETTER YA
	'𐵼' => '𐵼',
	// Added in 16.0 -- GARAY CAPITAL LETTER TA
	'𐵽' => '𐵽',
	// Added in 16.0 -- GARAY CAPITAL LETTER RA
	'𐵾' => '𐵾',
	// Added in 16.0 -- GARAY CAPITAL LETTER NYA
	'𐵿' => '𐵿',
	// Added in 16.0 -- GARAY CAPITAL LETTER FA
	'𐶀' => '𐶀',
	// Added in 16.0 -- GARAY CAPITAL LETTER NA
	'𐶁' => '𐶁',
	// Added in 16.0 -- GARAY CAPITAL LETTER PA
	'𐶂' => '𐶂',
	// Added in 16.0 -- GARAY CAPITAL LETTER HA
	'𐶃' => '𐶃',
	// Added in 16.0 -- GARAY CAPITAL LETTER OLD KA
	'𐶄' => '𐶄',
	// Added in 16.0 -- GARAY CAPITAL LETTER OLD NA
	'𐶅' => '𐶅',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER ARKAB
	'𖺻' => '𖺻',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER BASIGNA
	'𖺼' => '𖺼',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER DARBAI
	'𖺽' => '𖺽',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER EH
	'𖺾' => '𖺾',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER FITKO
	'𖺿' => '𖺿',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER GOWAY
	'𖻀' => '𖻀',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER HIRDEABO
	'𖻁' => '𖻁',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER I
	'𖻂' => '𖻂',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER DJAI
	'𖻃' => '𖻃',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER KOBO
	'𖻄' => '𖻄',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER LAKKO
	'𖻅' => '𖻅',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER MERI
	'𖻆' => '𖻆',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER NINI
	'𖻇' => '𖻇',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER GNA
	'𖻈' => '𖻈',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER NGAY
	'𖻉' => '𖻉',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER OI
	'𖻊' => '𖻊',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER PI
	'𖻋' => '𖻋',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER ERIGO
	'𖻌' => '𖻌',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER ERIGO TAMURA
	'𖻍' => '𖻍',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER SERI
	'𖻎' => '𖻎',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER SHEP
	'𖻏' => '𖻏',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER TATASOUE
	'𖻐' => '𖻐',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER UI
	'𖻑' => '𖻑',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER WASSE
	'𖻒' => '𖻒',
	// Added in 17.0 -- BERIA ERFE CAPITAL LETTER AY
	'𖻓' => '𖻓',
];
