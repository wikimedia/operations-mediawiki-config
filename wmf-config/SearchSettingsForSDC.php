<?php

// T218954
// Disable dispatching query builder, which provides specialized
// entity full text search, until it meets the commons use case.
$wgWBCSEnableDispatchingQueryBuilder = false;

// T257938
// mapping between search terms for haslicense: query and actual licence statements
$wgWBCSLicenseMapping = [
	'cc-by-sa' => [
		'P275=Q6905942', // copyright licence = CC-BY-SA
		'P275=Q98755369', // copyright licence = Commons Attribution-Share Alike 3.0 Brazil
		'P275=Q18810341', // copyright licence = Commons Attribution-ShareAlike 3.0 United States
		'P275=Q19068220', // copyright licence = Commons Attribution-ShareAlike 2.0 Generic
		'P275=Q14946043', // copyright licence = Commons Attribution-ShareAlike 3.0 Unported
		'P275=Q24331618', // copyright licence = Commons Attribution-ShareAlike 2.5 Canada
		'P275=Q56292840', // copyright licence = Commons Attribution-ShareAlike 3.0 IGO
		'P275=Q18199165', // copyright licence = Commons Attribution-ShareAlike 4.0 International
		'P275=Q19113751', // copyright licence = Commons Attribution-ShareAlike 2.5 Generic
		'P275=Q15914252', // copyright licence = Commons Attribution-ShareAlike 2.5 Sweden
		'P275=Q18195572', // copyright licence = Commons Attribution-ShareAlike 3.0 Netherlands
		'P275=Q18199175', // copyright licence = Commons Attribution-ShareAlike 2.5 Netherlands
		'P275=Q47001652', // copyright licence = Commons Attribution-ShareAlike 1.0 Generic
		'P275=Q42716613', // copyright licence = Commons Attribution-ShareAlike 3.0 Germany
		'P275=Q77014037', // copyright licence = Commons Attribution-ShareAlike 1.0 Netherlands
		'P275=Q77355872', // copyright licence = Commons Attribution-ShareAlike 2.0 France
		'P275=Q77367349', // copyright licence = Commons Attribution-ShareAlike 2.1 Japan
		'P275=Q44282641', // copyright licence = Commons Attribution-ShareAlike 2.0 South Korea
		'P275=Q76769447', // copyright licence = Commons Attribution-ShareAlike 1.0 Israel
		'P275=Q77135172', // copyright licence = Commons Attribution-ShareAlike 2.0 Canada
		'P275=Q77362254', // copyright licence = Commons Attribution-ShareAlike 2.0 Italy
		'P275=Q77363039', // copyright licence = Commons Attribution-ShareAlike 2.0 Japan
		'P275=Q77366576', // copyright licence = Commons Attribution-ShareAlike 2.1 Spain
		'P275=Q63340742', // copyright licence = Commons Attribution-ShareAlike 3.0 Norway
		'P275=Q76767348', // copyright licence = Commons Attribution-ShareAlike 1.0 Finland
		'P275=Q77363856', // copyright licence = Commons Attribution-ShareAlike 2.0 Netherlands
		'P275=Q77365183', // copyright licence = Commons Attribution-ShareAlike 2.0 UK: England & Wales
		'P275=Q86241082', // copyright licence = Commons Attribution-ShareAlike 3.0 Romania
		'P275=Q77131257', // copyright licence = Commons Attribution-ShareAlike 2.0 Australia
		'P275=Q77136299', // copyright licence = Commons Attribution-ShareAlike 2.0 Chile
		'P275=Q77143083', // copyright licence = Commons Attribution-ShareAlike 2.0 Germany
		'P275=Q77361415', // copyright licence = Commons Attribution-ShareAlike 2.0 Croatia
		'P275=Q77364488', // copyright licence = Commons Attribution-ShareAlike 2.0 Poland
		'P275=Q77364872', // copyright licence = Commons Attribution-ShareAlike 2.0 Taiwan
		'P275=Q86239991', // copyright licence = Commons Attribution-ShareAlike 3.0 Spain
		'P275=Q77365530', // copyright licence = Commons Attribution-ShareAlike 2.0 South Africa
		'P275=Q80837139', // copyright licence = Commons Attribution-ShareAlike 3.0 Austria
		'P275=Q80837607', // copyright licence = Commons Attribution-ShareAlike 3.0 Poland
		'P275=Q77021108', // copyright licence = Commons Attribution-ShareAlike 2.0 Austria
		'P275=Q77352646', // copyright licence = Commons Attribution-ShareAlike 2.0 Spain
		'P275=Q86239208', // copyright licence = Commons Attribution-ShareAlike 3.0 Australia
		'P275=Q86240624', // copyright licence = Commons Attribution-ShareAlike 3.0 Luxembourg
		'P275=Q77132386', // copyright licence = Commons Attribution-ShareAlike 2.0 Belgium
		'P275=Q77133402', // copyright licence = Commons Attribution-ShareAlike 2.0 Brazil
		'P275=Q77366066', // copyright licence = Commons Attribution-ShareAlike 2.1 Australia
		'P275=Q86239559', // copyright licence = Commons Attribution-ShareAlike 3.0 Estonia
		'P275=Q86240326', // copyright licence = Commons Attribution-ShareAlike 3.0 France
	],
	'cc-by' => [
		'P275=Q6905323', // copyright licence = CC-BY
		'P275=Q19125117', // copyright licence = Commons Attribution 2.0 Generic
		'P275=Q30942811', // copyright licence = Commons Attribution 1.0 Generic
		'P275=Q18810333', // copyright licence = Commons Attribution 2.5 Generic
		'P275=Q26116436', // copyright licence = Commons Attribution 2.1 Japan
		'P275=Q14947546', // copyright licence = Commons Attribution 3.0 Unported
		'P275=Q18810143', // copyright licence = Commons Attribution 3.0 United States
		'P275=Q44282633', // copyright licence = Commons Attribution 2.0 South Korea
		'P275=Q26259495', // copyright licence = Commons Attribution Intergovernmental Organizations licence 3.0
		'P275=Q53859967', // copyright licence = Commons Attribution 3.0 Netherlands
		'P275=Q20007257', // copyright licence = Commons Attribution 4.0 International
		'P275=Q52555753', // copyright licence = Commons Attribution 3.0 Australia
		'P275=Q27940776', // copyright licence = Commons Attribution 2.5 Sweden
		'P275=Q75457467', // copyright licence = Commons Attribution 2.0 Belgium
		'P275=Q75457506', // copyright licence = Commons Attribution 2.0 Brazil
		'P275=Q75460106', // copyright licence = Commons Attribution 2.0 Canada
		'P275=Q75665696', // copyright licence = Commons Attribution 2.5 Denmark
		'P275=Q75762418', // copyright licence = Commons Attribution 2.5 Mexico
		'P275=Q75764151', // copyright licence = Commons Attribution 2.5 Peru
		'P275=Q75764895', // copyright licence = Commons Attribution 2.5 Portugal
		'P275=Q75768706', // copyright licence = Commons Attribution 3.0 Austria
		'P275=Q75771320', // copyright licence = Commons Attribution 3.0 Switzerland
		'P275=Q75851799', // copyright licence = Commons Attribution 3.0 Greece
		'P275=Q75853514', // copyright licence = Commons Attribution 3.0 New Zealand
		'P275=Q75894644', // copyright licence = Commons Attribution 2.1 Spain
		'P275=Q75450165', // copyright licence = Commons Attribution 2.0 Austria
		'P275=Q75663969', // copyright licence = Commons Attribution 2.5 Columbia
		'P275=Q75761383', // copyright licence = Commons Attribution 2.5 Macedonia
		'P275=Q75766316', // copyright licence = Commons Attribution 2.5 Slovenia
		'P275=Q75771874', // copyright licence = Commons Attribution 3.0 Chile
		'P275=Q75775133', // copyright licence = Commons Attribution 3.0 Spain
		'P275=Q75779905', // copyright licence = Commons Attribution 3.0 Hong Kong
		'P275=Q75850813', // copyright licence = Commons Attribution 3.0 Estonia
		'P275=Q63241773', // copyright licence = Commons Attribution 2.0 UK: England & Wales
		'P275=Q75475677', // copyright licence = Commons Attribution 2.0 Italy
		'P275=Q62619894', // copyright licence = Commons Attribution 3.0 Germany
		'P275=Q75761779', // copyright licence = Commons Attribution 2.5 Malta
		'P275=Q75776487', // copyright licence = Commons Attribution 3.0 Italy
		'P275=Q76631753', // copyright licence = Commons Attribution 3.0 South Africa
		'P275=Q67918154', // copyright licence = Commons Attribution 3.0 Czech Republic
		'P275=Q75446609', // copyright licence = Commons Attribution 1.0 Israel
		'P275=Q75470422', // copyright licence = Commons Attribution 2.0 France
		'P275=Q75500112', // copyright licence = Commons Attribution 2.5 Bulgaria
		'P275=Q75504835', // copyright licence = Commons Attribution 2.5 Canada
		'P275=Q75705948', // copyright licence = Commons Attribution 2.5 Spain
		'P275=Q75706881', // copyright licence = Commons Attribution 2.5 Croatia
		'P275=Q75759387', // copyright licence = Commons Attribution 2.5 Hungary
		'P275=Q75759731', // copyright licence = Commons Attribution 2.5 Israel
		'P275=Q75767606', // copyright licence = Commons Attribution 2.5 South Africa
		'P275=Q75770766', // copyright licence = Commons Attribution 3.0 Brazil
		'P275=Q75777688', // copyright licence = Commons Attribution 3.0 Poland
		'P275=Q75852313', // copyright licence = Commons Attribution 3.0 Guatemala
		'P275=Q75856699', // copyright licence = Commons Attribution 3.0 Philippines
		'P275=Q75866892', // copyright licence = Commons Attribution 3.0 Thailand
		'P275=Q75882470', // copyright licence = Commons Attribution 3.0 Uganda
		'P275=Q75894680', // copyright licence = Commons Attribution 2.1 Australia
		'P275=Q75445499', // copyright licence = Commons Attribution 1.0 Netherlands
		'P275=Q75470365', // copyright licence = Commons Attribution 2.0 Spain
		'P275=Q75486069', // copyright licence = Commons Attribution 2.0 Poland
		'P275=Q75488238', // copyright licence = Commons Attribution 2.0 South Africa
		'P275=Q75491630', // copyright licence = Commons Attribution 2.5 Argentina
		'P275=Q75775714', // copyright licence = Commons Attribution 3.0 France
		'P275=Q75779562', // copyright licence = Commons Attribution 3.0 China Mainland
		'P275=Q75434631', // copyright licence = Commons Attribution 2.5 China Mainland
		'P275=Q75460149', // copyright licence = Commons Attribution 2.0 Chile
		'P275=Q75466259', // copyright licence = Commons Attribution 2.0 Germany
		'P275=Q75476747', // copyright licence = Commons Attribution 2.0 Netherlands
		'P275=Q75501683', // copyright licence = Commons Attribution 2.5 Brazil
		'P275=Q75762784', // copyright licence = Commons Attribution 2.5 Malaysia
		'P275=Q75776014', // copyright licence = Commons Attribution 3.0 Croatia
		'P275=Q75789929', // copyright licence = Commons Attribution 3.0 Costa Rica
		'P275=Q75850366', // copyright licence = Commons Attribution 3.0 Ecuador
		'P275=Q75853549', // copyright licence = Commons Attribution 3.0 Norway
		'P275=Q75857518', // copyright licence = Commons Attribution 3.0 Puerto Rico
		'P275=Q75477775', // copyright licence = Commons Attribution 2.0 Japan
		'P275=Q75487055', // copyright licence = Commons Attribution 2.0 Taiwan
		'P275=Q75494411', // copyright licence = Commons Attribution 2.5 Australia
		'P275=Q75506669', // copyright licence = Commons Attribution 2.5 Switzerland
		'P275=Q75763101', // copyright licence = Commons Attribution 2.5 Netherlands
		'P275=Q75778801', // copyright licence = Commons Attribution 3.0 Taiwan
		'P275=Q75852938', // copyright licence = Commons Attribution 3.0 Ireland
		'P275=Q75854323', // copyright licence = Commons Attribution 3.0 Portugal
		'P275=Q75858169', // copyright licence = Commons Attribution 3.0 Romania
		'P275=Q75859019', // copyright licence = Commons Attribution 3.0 Serbia
		'P275=Q75443434', // copyright licence = Commons Attribution 2.5 India
		'P275=Q75446635', // copyright licence = Commons Attribution 1.0 Finland
		'P275=Q75452310', // copyright licence = Commons Attribution 2.0 Australia
		'P275=Q75474094', // copyright licence = Commons Attribution 2.0 Croatia
		'P275=Q75760479', // copyright licence = Commons Attribution 2.5 Italy
		'P275=Q75764470', // copyright licence = Commons Attribution 2.5 Poland
		'P275=Q75765287', // copyright licence = Commons Attribution 2.5 UK: Scotland
		'P275=Q75767185', // copyright licence = Commons Attribution 2.5 Taiwan
		'P275=Q75850832', // copyright licence = Commons Attribution 3.0 Egypt
		'P275=Q75853187', // copyright licence = Commons Attribution 3.0 Luxembourg
		'P275=Q75859751', // copyright licence = Commons Attribution 3.0 Singapore
		'P275=Q75889409', // copyright licence = Commons Attribution 3.0 Vietnam
	],
	'unrestricted' => [
		'P275=Q98341313', // copyright licence = Kopimi
		'P275=Q98592850', // copyright licence = released into the public domain by the copyright holder
		'P275=Q24273512', // copyright licence = Public Domain Dedication and License v1.0
		'P275=Q6938433', // copyright licence = CC0
		'P275=Q152481', // copyright licence = WTFPL
		'P275=Q7257361', // copyright licence = Creative Commons Public Domain Mark
		'P275=Q21659044', // copyright licence = Unlicense
		'P275=Q67538600', // copyright licence = MIT No Attribution License
		'P275=Q10249', // copyright licence = Beerware
		'P275=Q48271011', // copyright licence = Zero-clause BSD License
		'P6216=Q19652', // copyright status = public domain
	],
];

if ( $wmfRealm === 'labs' ) {
	$wgWBCSLicenseMapping = [];
}
