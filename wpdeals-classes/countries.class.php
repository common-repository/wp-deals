<?php
/**
 * WPDeals countries
 * 
 * The WPDeals countries class stores country/state data.
 *
 * @class 		wpdeals_countries
 * @package		WPDeals
 * @category	Class
 * @author		Tokokoo
 */
class wpdeals_countries {
	
	var $countries;
	var $states;
	
	/**
	 * Constructor
	 */
	function __construct() {
	
		$this->countries = array(
			'AD' => __('Andorra', 'wpdeals'),
                        'AE' => __('United Arab Emirates', 'wpdeals'),
			'AF' => __('Afghanistan', 'wpdeals'),
			'AG' => __('Antigua and Barbuda', 'wpdeals'),
			'AI' => __('Anguilla', 'wpdeals'),
			'AL' => __('Albania', 'wpdeals'),
			'AM' => __('Armenia', 'wpdeals'),
			'AN' => __('Netherlands Antilles', 'wpdeals'),
			'AO' => __('Angola', 'wpdeals'),
			'AQ' => __('Antarctica', 'wpdeals'),
			'AR' => __('Argentina', 'wpdeals'),
			'AS' => __('American Samoa', 'wpdeals'),
			'AT' => __('Austria', 'wpdeals'),
			'AU' => __('Australia', 'wpdeals'),
			'AW' => __('Aruba', 'wpdeals'),
			'AX' => __('Aland Islands', 'wpdeals'),
			'AZ' => __('Azerbaijan', 'wpdeals'),
			'BA' => __('Bosnia and Herzegovina', 'wpdeals'),
			'BB' => __('Barbados', 'wpdeals'),
			'BD' => __('Bangladesh', 'wpdeals'),
			'BE' => __('Belgium', 'wpdeals'),
			'BF' => __('Burkina Faso', 'wpdeals'),
			'BG' => __('Bulgaria', 'wpdeals'),
			'BH' => __('Bahrain', 'wpdeals'),
			'BI' => __('Burundi', 'wpdeals'),
			'BJ' => __('Benin', 'wpdeals'),
			'BL' => __('Saint Barth‚àö¬©lemy', 'wpdeals'),
			'BM' => __('Bermuda', 'wpdeals'),
			'BN' => __('Brunei', 'wpdeals'),
			'BO' => __('Bolivia', 'wpdeals'),
			'BR' => __('Brazil', 'wpdeals'),
			'BS' => __('Bahamas', 'wpdeals'),
			'BT' => __('Bhutan', 'wpdeals'),
			'BV' => __('Bouvet Island', 'wpdeals'),
			'BW' => __('Botswana', 'wpdeals'),
			'BY' => __('Belarus', 'wpdeals'),
			'BZ' => __('Belize', 'wpdeals'),
			'CA' => __('Canada', 'wpdeals'),
			'CC' => __('Cocos (Keeling) Islands', 'wpdeals'),
			'CD' => __('Congo (Kinshasa)', 'wpdeals'),
			'CF' => __('Central African Republic', 'wpdeals'),
			'CG' => __('Congo (Brazzaville)', 'wpdeals'),
			'CH' => __('Switzerland', 'wpdeals'),
			'CI' => __('Ivory Coast', 'wpdeals'),
			'CK' => __('Cook Islands', 'wpdeals'),
			'CL' => __('Chile', 'wpdeals'),
			'CM' => __('Cameroon', 'wpdeals'),
			'CN' => __('China', 'wpdeals'),
			'CO' => __('Colombia', 'wpdeals'),
			'CR' => __('Costa Rica', 'wpdeals'),
			'CU' => __('Cuba', 'wpdeals'),
			'CV' => __('Cape Verde', 'wpdeals'),
			'CX' => __('Christmas Island', 'wpdeals'),
			'CY' => __('Cyprus', 'wpdeals'),
			'CZ' => __('Czech Republic', 'wpdeals'),
			'DE' => __('Germany', 'wpdeals'),
			'DJ' => __('Djibouti', 'wpdeals'),
			'DK' => __('Denmark', 'wpdeals'),
			'DM' => __('Dominica', 'wpdeals'),
			'DO' => __('Dominican Republic', 'wpdeals'),
			'DZ' => __('Algeria', 'wpdeals'),
			'EC' => __('Ecuador', 'wpdeals'),
			'EE' => __('Estonia', 'wpdeals'),
			'EG' => __('Egypt', 'wpdeals'),
			'EH' => __('Western Sahara', 'wpdeals'),
			'ER' => __('Eritrea', 'wpdeals'),
			'ES' => __('Spain', 'wpdeals'),
			'ET' => __('Ethiopia', 'wpdeals'),
			'FI' => __('Finland', 'wpdeals'),
			'FJ' => __('Fiji', 'wpdeals'),
			'FK' => __('Falkland Islands', 'wpdeals'),
			'FM' => __('Micronesia', 'wpdeals'),
			'FO' => __('Faroe Islands', 'wpdeals'),
			'FR' => __('France', 'wpdeals'),
			'GA' => __('Gabon', 'wpdeals'),
			'GB' => __('United Kingdom', 'wpdeals'),
			'GD' => __('Grenada', 'wpdeals'),
			'GE' => __('Georgia', 'wpdeals'),
			'GF' => __('French Guiana', 'wpdeals'),
			'GG' => __('Guernsey', 'wpdeals'),
			'GH' => __('Ghana', 'wpdeals'),
			'GI' => __('Gibraltar', 'wpdeals'),
			'GL' => __('Greenland', 'wpdeals'),
			'GM' => __('Gambia', 'wpdeals'),
			'GN' => __('Guinea', 'wpdeals'),
			'GP' => __('Guadeloupe', 'wpdeals'),
			'GQ' => __('Equatorial Guinea', 'wpdeals'),
			'GR' => __('Greece', 'wpdeals'),
			'GS' => __('South Georgia/Sandwich Islands', 'wpdeals'),
			'GT' => __('Guatemala', 'wpdeals'),
			'GU' => __('Guam', 'wpdeals'),
			'GW' => __('Guinea-Bissau', 'wpdeals'),
			'GY' => __('Guyana', 'wpdeals'),
			'HK' => __('Hong Kong S.A.R., China', 'wpdeals'),
			//'HM' => __('Heard Island and McDonald Islands', 'wpdeals'), // Uninhabitted :)
			'HN' => __('Honduras', 'wpdeals'),
			'HR' => __('Croatia', 'wpdeals'),
			'HT' => __('Haiti', 'wpdeals'),
			'HU' => __('Hungary', 'wpdeals'),
			'ID' => __('Indonesia', 'wpdeals'),
			'IE' => __('Ireland', 'wpdeals'),
			'IL' => __('Israel', 'wpdeals'),
			'IM' => __('Isle of Man', 'wpdeals'),
			'IN' => __('India', 'wpdeals'),
			'IO' => __('British Indian Ocean Territory', 'wpdeals'),
			'IQ' => __('Iraq', 'wpdeals'),
			'IR' => __('Iran', 'wpdeals'),
			'IS' => __('Iceland', 'wpdeals'),
			'IT' => __('Italy', 'wpdeals'),
			'JE' => __('Jersey', 'wpdeals'),
			'JM' => __('Jamaica', 'wpdeals'),
			'JO' => __('Jordan', 'wpdeals'),
			'JP' => __('Japan', 'wpdeals'),
			'KE' => __('Kenya', 'wpdeals'),
			'KG' => __('Kyrgyzstan', 'wpdeals'),
			'KH' => __('Cambodia', 'wpdeals'),
			'KI' => __('Kiribati', 'wpdeals'),
			'KM' => __('Comoros', 'wpdeals'),
			'KN' => __('Saint Kitts and Nevis', 'wpdeals'),
			'KP' => __('North Korea', 'wpdeals'),
			'KR' => __('South Korea', 'wpdeals'),
			'KW' => __('Kuwait', 'wpdeals'),
			'KY' => __('Cayman Islands', 'wpdeals'),
			'KZ' => __('Kazakhstan', 'wpdeals'),
			'LA' => __('Laos', 'wpdeals'),
			'LB' => __('Lebanon', 'wpdeals'),
			'LC' => __('Saint Lucia', 'wpdeals'),
			'LI' => __('Liechtenstein', 'wpdeals'),
			'LK' => __('Sri Lanka', 'wpdeals'),
			'LR' => __('Liberia', 'wpdeals'),
			'LS' => __('Lesotho', 'wpdeals'),
			'LT' => __('Lithuania', 'wpdeals'),
			'LU' => __('Luxembourg', 'wpdeals'),
			'LV' => __('Latvia', 'wpdeals'),
			'LY' => __('Libya', 'wpdeals'),
			'MA' => __('Morocco', 'wpdeals'),
			'MC' => __('Monaco', 'wpdeals'),
			'MD' => __('Moldova', 'wpdeals'),
			'ME' => __('Montenegro', 'wpdeals'),
			'MF' => __('Saint Martin (French part)', 'wpdeals'),
			'MG' => __('Madagascar', 'wpdeals'),
			'MH' => __('Marshall Islands', 'wpdeals'),
			'MK' => __('Macedonia', 'wpdeals'),
			'ML' => __('Mali', 'wpdeals'),
			'MM' => __('Myanmar', 'wpdeals'),
			'MN' => __('Mongolia', 'wpdeals'),
			'MO' => __('Macao S.A.R., China', 'wpdeals'),
			'MP' => __('Northern Mariana Islands', 'wpdeals'),
			'MQ' => __('Martinique', 'wpdeals'),
			'MR' => __('Mauritania', 'wpdeals'),
			'MS' => __('Montserrat', 'wpdeals'),
			'MT' => __('Malta', 'wpdeals'),
			'MU' => __('Mauritius', 'wpdeals'),
			'MV' => __('Maldives', 'wpdeals'),
			'MW' => __('Malawi', 'wpdeals'),
			'MX' => __('Mexico', 'wpdeals'),
			'MY' => __('Malaysia', 'wpdeals'),
			'MZ' => __('Mozambique', 'wpdeals'),
			'NA' => __('Namibia', 'wpdeals'),
			'NC' => __('New Caledonia', 'wpdeals'),
			'NE' => __('Niger', 'wpdeals'),
			'NF' => __('Norfolk Island', 'wpdeals'),
			'NG' => __('Nigeria', 'wpdeals'),
			'NI' => __('Nicaragua', 'wpdeals'),
			'NL' => __('Netherlands', 'wpdeals'),
			'NO' => __('Norway', 'wpdeals'),
			'NP' => __('Nepal', 'wpdeals'),
			'NR' => __('Nauru', 'wpdeals'),
			'NU' => __('Niue', 'wpdeals'),
			'NZ' => __('New Zealand', 'wpdeals'),
			'OM' => __('Oman', 'wpdeals'),
			'PA' => __('Panama', 'wpdeals'),
			'PE' => __('Peru', 'wpdeals'),
			'PF' => __('French Polynesia', 'wpdeals'),
			'PG' => __('Papua New Guinea', 'wpdeals'),
			'PH' => __('Philippines', 'wpdeals'),
			'PK' => __('Pakistan', 'wpdeals'),
			'PL' => __('Poland', 'wpdeals'),
			'PM' => __('Saint Pierre and Miquelon', 'wpdeals'),
			'PN' => __('Pitcairn', 'wpdeals'),
			'PR' => __('Puerto Rico', 'wpdeals'),
			'PS' => __('Palestinian Territory', 'wpdeals'),
			'PT' => __('Portugal', 'wpdeals'),
			'PW' => __('Palau', 'wpdeals'),
			'PY' => __('Paraguay', 'wpdeals'),
			'QA' => __('Qatar', 'wpdeals'),
			'RE' => __('Reunion', 'wpdeals'),
			'RO' => __('Romania', 'wpdeals'),
			'RS' => __('Serbia', 'wpdeals'),
			'RU' => __('Russia', 'wpdeals'),
			'RW' => __('Rwanda', 'wpdeals'),
			'SA' => __('Saudi Arabia', 'wpdeals'),
			'SB' => __('Solomon Islands', 'wpdeals'),
			'SC' => __('Seychelles', 'wpdeals'),
			'SD' => __('Sudan', 'wpdeals'),
			'SE' => __('Sweden', 'wpdeals'),
			'SG' => __('Singapore', 'wpdeals'),
			'SH' => __('Saint Helena', 'wpdeals'),
			'SI' => __('Slovenia', 'wpdeals'),
			'SJ' => __('Svalbard and Jan Mayen', 'wpdeals'),
			'SK' => __('Slovakia', 'wpdeals'),
			'SL' => __('Sierra Leone', 'wpdeals'),
			'SM' => __('San Marino', 'wpdeals'),
			'SN' => __('Senegal', 'wpdeals'),
			'SO' => __('Somalia', 'wpdeals'),
			'SR' => __('Suriname', 'wpdeals'),
			'ST' => __('Sao Tome and Principe', 'wpdeals'),
			'SV' => __('El Salvador', 'wpdeals'),
			'SY' => __('Syria', 'wpdeals'),
			'SZ' => __('Swaziland', 'wpdeals'),
			'TC' => __('Turks and Caicos Islands', 'wpdeals'),
			'TD' => __('Chad', 'wpdeals'),
			'TF' => __('French Southern Territories', 'wpdeals'),
			'TG' => __('Togo', 'wpdeals'),
			'TH' => __('Thailand', 'wpdeals'),
			'TJ' => __('Tajikistan', 'wpdeals'),
			'TK' => __('Tokelau', 'wpdeals'),
			'TL' => __('Timor-Leste', 'wpdeals'),
			'TM' => __('Turkmenistan', 'wpdeals'),
			'TN' => __('Tunisia', 'wpdeals'),
			'TO' => __('Tonga', 'wpdeals'),
			'TR' => __('Turkey', 'wpdeals'),
			'TT' => __('Trinidad and Tobago', 'wpdeals'),
			'TV' => __('Tuvalu', 'wpdeals'),
			'TW' => __('Taiwan', 'wpdeals'),
			'TZ' => __('Tanzania', 'wpdeals'),
			'UA' => __('Ukraine', 'wpdeals'),
			'UG' => __('Uganda', 'wpdeals'),
			'UM' => __('US Minor Outlying Islands', 'wpdeals'),
			'US' => __('United States', 'wpdeals'),
			'USAF' => __('US Armed Forces', 'wpdeals'), 
			'UY' => __('Uruguay', 'wpdeals'),
			'UZ' => __('Uzbekistan', 'wpdeals'),
			'VA' => __('Vatican', 'wpdeals'),
			'VC' => __('Saint Vincent and the Grenadines', 'wpdeals'),
			'VE' => __('Venezuela', 'wpdeals'),
			'VG' => __('British Virgin Islands', 'wpdeals'),
			'VI' => __('U.S. Virgin Islands', 'wpdeals'),
			'VN' => __('Vietnam', 'wpdeals'),
			'VU' => __('Vanuatu', 'wpdeals'),
			'WF' => __('Wallis and Futuna', 'wpdeals'),
			'WS' => __('Samoa', 'wpdeals'),
			'YE' => __('Yemen', 'wpdeals'),
			'YT' => __('Mayotte', 'wpdeals'),
			'ZA' => __('South Africa', 'wpdeals'),
			'ZM' => __('Zambia', 'wpdeals'),
			'ZW' => __('Zimbabwe', 'wpdeals')
		);
		
		$this->states = array(
			'AU' => array(
				'ACT' => __('Australian Capital Territory', 'wpdeals') ,
				'NSW' => __('New South Wales', 'wpdeals') ,
				'NT' => __('Northern Territory', 'wpdeals') ,
				'QLD' => __('Queensland', 'wpdeals') ,
				'SA' => __('South Australia', 'wpdeals') ,
				'TAS' => __('Tasmania', 'wpdeals') ,
				'VIC' => __('Victoria', 'wpdeals') ,
				'WA' => __('Western Australia', 'wpdeals') 
			),
			'BR' => array(
			    'AM' => __('Amazonas', 'wpdeals'),
			    'AC' => __('Acre', 'wpdeals'),
			    'AL' => __('Alagoas', 'wpdeals'),
			    'AP' => __('Amap&aacute;', 'wpdeals'),
			    'CE' => __('Cear&aacute;', 'wpdeals'),
			    'DF' => __('Distrito federal', 'wpdeals'),
			    'ES' => __('Espirito santo', 'wpdeals'),
			    'MA' => __('Maranh&atilde;o', 'wpdeals'),
			    'PR' => __('Paran&aacute;', 'wpdeals'),
			    'PE' => __('Pernambuco', 'wpdeals'),
			    'PI' => __('Piau&iacute;', 'wpdeals'),
			    'RN' => __('Rio grande do norte', 'wpdeals'),
			    'RS' => __('Rio grande do sul', 'wpdeals'),
			    'RO' => __('Rond&ocirc;nia', 'wpdeals'),
			    'RR' => __('Roraima', 'wpdeals'),
			    'SC' => __('Santa catarina', 'wpdeals'),
			    'SE' => __('Sergipe', 'wpdeals'),
			    'TO' => __('Tocantins', 'wpdeals'),
			    'PA' => __('Par&aacute;', 'wpdeals'),
			    'BH' => __('Bahia', 'wpdeals'),
			    'GO' => __('Goi&aacute;s', 'wpdeals'),
			    'MT' => __('Mato grosso', 'wpdeals'),
			    'MS' => __('Mato grosso do sul', 'wpdeals'),
			    'RJ' => __('Rio de janeiro', 'wpdeals'),
			    'SP' => __('S&atilde;o paulo', 'wpdeals'),
			    'RS' => __('Rio grande do sul', 'wpdeals'),
			    'MG' => __('Minas gerais', 'wpdeals'),
			    'PB' => __('Paraiba', 'wpdeals'),
			),
			'CA' => array(
				'AB' => __('Alberta', 'wpdeals') ,
				'BC' => __('British Columbia', 'wpdeals') ,
				'MB' => __('Manitoba', 'wpdeals') ,
				'NB' => __('New Brunswick', 'wpdeals') ,
				'NF' => __('Newfoundland', 'wpdeals') ,
				'NT' => __('Northwest Territories', 'wpdeals') ,
				'NS' => __('Nova Scotia', 'wpdeals') ,
				'NU' => __('Nunavut', 'wpdeals') ,
				'ON' => __('Ontario', 'wpdeals') ,
				'PE' => __('Prince Edward Island', 'wpdeals') ,
				'PQ' => __('Quebec', 'wpdeals') ,
				'SK' => __('Saskatchewan', 'wpdeals') ,
				'YT' => __('Yukon Territory', 'wpdeals') 
			),
			/*'GB' => array(
				'England' => array(
					'Avon' => __('Avon', 'wpdeals'),
					'Bedfordshire' => __('Bedfordshire', 'wpdeals'),
					'Berkshire' => __('Berkshire', 'wpdeals'),
					'Bristol' => __('Bristol', 'wpdeals'),
					'Buckinghamshire' => __('Buckinghamshire', 'wpdeals'),
					'Cambridgeshire' => __('Cambridgeshire', 'wpdeals'),
					'Cheshire' => __('Cheshire', 'wpdeals'),
					'Cleveland' => __('Cleveland', 'wpdeals'),
					'Cornwall' => __('Cornwall', 'wpdeals'),
					'Cumbria' => __('Cumbria', 'wpdeals'),
					'Derbyshire' => __('Derbyshire', 'wpdeals'),
					'Devon' => __('Devon', 'wpdeals'),
					'Dorset' => __('Dorset', 'wpdeals'),
					'Durham' => __('Durham', 'wpdeals'),
					'East Riding of Yorkshire' => __('East Riding of Yorkshire', 'wpdeals'),
					'East Sussex' => __('East Sussex', 'wpdeals'),
					'Essex' => __('Essex', 'wpdeals'),
					'Gloucestershire' => __('Gloucestershire', 'wpdeals'),
					'Greater Manchester' => __('Greater Manchester', 'wpdeals'),
					'Hampshire' => __('Hampshire', 'wpdeals'),
					'Herefordshire' => __('Herefordshire', 'wpdeals'),
					'Hertfordshire' => __('Hertfordshire', 'wpdeals'),
					'Humberside' => __('Humberside', 'wpdeals'),
					'Isle of Wight' => __('Isle of Wight', 'wpdeals'),
					'Isles of Scilly' => __('Isles of Scilly', 'wpdeals'),
					'Kent' => __('Kent', 'wpdeals'),
					'Lancashire' => __('Lancashire', 'wpdeals'),
					'Leicestershire' => __('Leicestershire', 'wpdeals'),
					'Lincolnshire' => __('Lincolnshire', 'wpdeals'),
					'London' => __('London', 'wpdeals'),
					'Merseyside' => __('Merseyside', 'wpdeals'),
					'Middlesex' => __('Middlesex', 'wpdeals'),
					'Norfolk' => __('Norfolk', 'wpdeals'),
					'North Yorkshire' => __('North Yorkshire', 'wpdeals'),
					'Northamptonshire' => __('Northamptonshire', 'wpdeals'),
					'Northumberland' => __('Northumberland', 'wpdeals'),
					'Nottinghamshire' => __('Nottinghamshire', 'wpdeals'),
					'Oxfordshire' => __('Oxfordshire', 'wpdeals'),
					'Rutland' => __('Rutland', 'wpdeals'),
					'Shropshire' => __('Shropshire', 'wpdeals'),
					'Somerset' => __('Somerset', 'wpdeals'),
					'South Yorkshire' => __('South Yorkshire', 'wpdeals'),
					'Staffordshire' => __('Staffordshire', 'wpdeals'),
					'Suffolk' => __('Suffolk', 'wpdeals'),
					'Surrey' => __('Surrey', 'wpdeals'),
					'Tyne and Wear' => __('Tyne and Wear', 'wpdeals'),
					'Warwickshire' => __('Warwickshire', 'wpdeals'),
					'West Midlands' => __('West Midlands', 'wpdeals'),
					'West Sussex' => __('West Sussex', 'wpdeals'),
					'West Yorkshire' => __('West Yorkshire', 'wpdeals'),
					'Wiltshire' => __('Wiltshire', 'wpdeals'),
					'Worcestershire' => __('Worcestershire', 'wpdeals')
				),
				'Northern Ireland' => array(
					'Antrim' => __('Antrim', 'wpdeals'),
					'Armagh' => __('Armagh', 'wpdeals'),
					'Down' => __('Down', 'wpdeals'),
					'Fermanagh' => __('Fermanagh', 'wpdeals'),
					'Londonderry' => __('Londonderry', 'wpdeals'),
					'Tyrone' => __('Tyrone', 'wpdeals')
				),
				'Scotland' => array(
					'Aberdeen City' => __('Aberdeen City', 'wpdeals'),
					'Aberdeenshire' => __('Aberdeenshire', 'wpdeals'),
					'Angus' => __('Angus', 'wpdeals'),
					'Argyll and Bute' => __('Argyll and Bute', 'wpdeals'),
					'Clackmannan' => __('Clackmannan', 'wpdeals'),
					'Dumfries and Galloway' => __('Dumfries and Galloway', 'wpdeals'),
					'East Ayrshire' => __('East Ayrshire', 'wpdeals'),
					'East Dunbartonshire' => __('East Dunbartonshire', 'wpdeals'),
					'East Lothian' => __('East Lothian', 'wpdeals'),
					'East Renfrewshire' => __('East Renfrewshire', 'wpdeals'),
					'Edinburgh City' => __('Edinburgh City', 'wpdeals'),
					'Falkirk' => __('Falkirk', 'wpdeals'),
					'Fife' => __('Fife', 'wpdeals'),
					'Glasgow' => __('Glasgow', 'wpdeals'),
					'Highland' => __('Highland', 'wpdeals'),
					'Inverclyde' => __('Inverclyde', 'wpdeals'),
					'Midlothian' => __('Midlothian', 'wpdeals'),
					'Moray' => __('Moray', 'wpdeals'),
					'North Ayrshire' => __('North Ayrshire', 'wpdeals'),
					'North Lanarkshire' => __('North Lanarkshire', 'wpdeals'),
					'Orkney' => __('Orkney', 'wpdeals'),
					'Perthshire and Kinross' => __('Perthshire and Kinross', 'wpdeals'),
					'Renfrewshire' => __('Renfrewshire', 'wpdeals'),
					'Roxburghshire' => __('Roxburghshire', 'wpdeals'),
					'Shetland' => __('Shetland', 'wpdeals'),
					'South Ayrshire' => __('South Ayrshire', 'wpdeals'),
					'South Lanarkshire' => __('South Lanarkshire', 'wpdeals'),
					'Stirling' => __('Stirling', 'wpdeals'),
					'West Dunbartonshire' => __('West Dunbartonshire', 'wpdeals'),
					'West Lothian' => __('West Lothian', 'wpdeals'),
					'Western Isles' => __('Western Isles', 'wpdeals'),
				),
				'Wales' => array(
					'Blaenau Gwent' => __('Blaenau Gwent', 'wpdeals'),
					'Bridgend' => __('Bridgend', 'wpdeals'),
					'Caerphilly' => __('Caerphilly', 'wpdeals'),
					'Cardiff' => __('Cardiff', 'wpdeals'),
					'Carmarthenshire' => __('Carmarthenshire', 'wpdeals'),
					'Ceredigion' => __('Ceredigion', 'wpdeals'),
					'Conwy' => __('Conwy', 'wpdeals'),
					'Denbighshire' => __('Denbighshire', 'wpdeals'),
					'Flintshire' => __('Flintshire', 'wpdeals'),
					'Gwynedd' => __('Gwynedd', 'wpdeals'),
					'Isle of Anglesey' => __('Isle of Anglesey', 'wpdeals'),
					'Merthyr Tydfil' => __('Merthyr Tydfil', 'wpdeals'),
					'Monmouthshire' => __('Monmouthshire', 'wpdeals'),
					'Neath Port Talbot' => __('Neath Port Talbot', 'wpdeals'),
					'Newport' => __('Newport', 'wpdeals'),
					'Pembrokeshire' => __('Pembrokeshire', 'wpdeals'),
					'Powys' => __('Powys', 'wpdeals'),
					'Rhondda Cynon Taff' => __('Rhondda Cynon Taff', 'wpdeals'),
					'Swansea' => __('Swansea', 'wpdeals'),
					'Torfaen' => __('Torfaen', 'wpdeals'),
					'The Vale of Glamorgan' => __('The Vale of Glamorgan', 'wpdeals'),
					'Wrexham' => __('Wrexham', 'wpdeals')
				)
			),*/
			'US' => array(
				'AL' => __('Alabama', 'wpdeals') ,
				'AK' => __('Alaska', 'wpdeals') ,
				'AZ' => __('Arizona', 'wpdeals') ,
				'AR' => __('Arkansas', 'wpdeals') ,
				'CA' => __('California', 'wpdeals') ,
				'CO' => __('Colorado', 'wpdeals') ,
				'CT' => __('Connecticut', 'wpdeals') ,
				'DE' => __('Delaware', 'wpdeals') ,
				'DC' => __('District Of Columbia', 'wpdeals') ,
				'FL' => __('Florida', 'wpdeals') ,
				'GA' => __('Georgia', 'wpdeals') ,
				'HI' => __('Hawaii', 'wpdeals') ,
				'ID' => __('Idaho', 'wpdeals') ,
				'IL' => __('Illinois', 'wpdeals') ,
				'IN' => __('Indiana', 'wpdeals') ,
				'IA' => __('Iowa', 'wpdeals') ,
				'KS' => __('Kansas', 'wpdeals') ,
				'KY' => __('Kentucky', 'wpdeals') ,
				'LA' => __('Louisiana', 'wpdeals') ,
				'ME' => __('Maine', 'wpdeals') ,
				'MD' => __('Maryland', 'wpdeals') ,
				'MA' => __('Massachusetts', 'wpdeals') ,
				'MI' => __('Michigan', 'wpdeals') ,
				'MN' => __('Minnesota', 'wpdeals') ,
				'MS' => __('Mississippi', 'wpdeals') ,
				'MO' => __('Missouri', 'wpdeals') ,
				'MT' => __('Montana', 'wpdeals') ,
				'NE' => __('Nebraska', 'wpdeals') ,
				'NV' => __('Nevada', 'wpdeals') ,
				'NH' => __('New Hampshire', 'wpdeals') ,
				'NJ' => __('New Jersey', 'wpdeals') ,
				'NM' => __('New Mexico', 'wpdeals') ,
				'NY' => __('New York', 'wpdeals') ,
				'NC' => __('North Carolina', 'wpdeals') ,
				'ND' => __('North Dakota', 'wpdeals') ,
				'OH' => __('Ohio', 'wpdeals') ,
				'OK' => __('Oklahoma', 'wpdeals') ,
				'OR' => __('Oregon', 'wpdeals') ,
				'PA' => __('Pennsylvania', 'wpdeals') ,
				'RI' => __('Rhode Island', 'wpdeals') ,
				'SC' => __('South Carolina', 'wpdeals') ,
				'SD' => __('South Dakota', 'wpdeals') ,
				'TN' => __('Tennessee', 'wpdeals') ,
				'TX' => __('Texas', 'wpdeals') ,
				'UT' => __('Utah', 'wpdeals') ,
				'VT' => __('Vermont', 'wpdeals') ,
				'VA' => __('Virginia', 'wpdeals') ,
				'WA' => __('Washington', 'wpdeals') ,
				'WV' => __('West Virginia', 'wpdeals') ,
				'WI' => __('Wisconsin', 'wpdeals') ,
				'WY' => __('Wyoming', 'wpdeals') 
			),
			'USAF' => array(
				'AA' => __('Americas', 'wpdeals') ,
				'AE' => __('Europe', 'wpdeals') ,
				'AP' => __('Pacific', 'wpdeals') 
			)
		);
		
		asort($this->countries);

	}
	
	/** get base country */
	function get_base_country() {
		$default = get_option('wpdeals_default_country');
    	if (strstr($default, ':')) :
    		$country = current(explode(':', $default));
    		$state = end(explode(':', $default));
    	else :
    		$country = $default;
    		$state = '';
    	endif;
		
		return $country;	    	
	}
	
	/** get base state */
	function get_base_state() {
		$default = get_option('wpdeals_default_country');
    	if (strstr($default, ':')) :
    		$country = current(explode(':', $default));
    		$state = end(explode(':', $default));
    	else :
    		$country = $default;
    		$state = '';
    	endif;
		
		return $state;	    	
	}
	
	/** get countries we allow only */
	function get_allowed_countries() {
	
		$countries = $this->countries;
		
		if (get_option('wpdeals_allowed_countries')!=='specific') return $countries;

		$allowed_countries = array();
		
		$allowed_countries_raw = get_option('wpdeals_specific_allowed_countries');
		
		foreach ($allowed_countries_raw as $country) :
			
			$allowed_countries[$country] = $countries[$country];
			
		endforeach;
		
		asort($allowed_countries);
		
		return $allowed_countries;
	}
	
	/** Gets an array of countries in the EU */
	function get_european_union_countries() {
		return array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK');
	}
	
	/** Gets the correct string for shipping - ether 'to the' or 'to' */
	function shipping_to_prefix() {
		global $wpdeals;
		$return = '';
		if (in_array($wpdeals->customer->get_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __('to the', 'wpdeals');
		else $return = __('to', 'wpdeals');
		return apply_filters('wpdeals_countries_shipping_to_prefix', $return, $wpdeals->customer->get_shipping_country());
	}
	
	/** Prefix certain countries with 'the' */
	function estimated_for_prefix() {
		global $wpdeals;
		$return = '';
		if (in_array($wpdeals->customer->get_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __('the', 'wpdeals') . ' ';
		return apply_filters('wpdeals_countries_estimated_for_prefix', $return, $wpdeals->customer->get_shipping_country());
	}
	
	/** Correctly name tax in some countries VAT on the frontend */
	function tax_or_vat() {
		global $wpdeals;
		
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __('VAT', 'wpdeals') : __('Tax', 'wpdeals');
		
		return apply_filters('wpdeals_countries_tax_or_vat', $return);
	}
	
	function inc_tax_or_vat( $rate = false ) {
		global $wpdeals;
		
		if ( $rate > 0 || $rate === 0 ) :
			$rate = rtrim(rtrim($rate, '0'), '.');
			if (!$rate) $rate = 0;
			$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? sprintf(__('(inc. %s%% VAT)', 'wpdeals'), $rate) : sprintf(__('(inc. %s%% tax)', 'wpdeals'), $rate);
		else :
			$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __('(inc. VAT)', 'wpdeals') : __('(inc. tax)', 'wpdeals');
		endif;
		
		return apply_filters('wpdeals_countries_inc_tax_or_vat', $return, $rate);
	}
	
	function ex_tax_or_vat() {
		global $wpdeals;
		
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __('(ex. VAT)', 'wpdeals') : __('(ex. tax)', 'wpdeals');
		
		return apply_filters('wpdeals_countries_ex_tax_or_vat', $return);
	}
	
	/** get states */
	function get_states( $cc ) {
		if (isset( $this->states[$cc] )) return $this->states[$cc];
	}
	
	/** Outputs the list of countries and states for use in dropdown boxes */
	function country_dropdown_options( $selected_country = '', $selected_state = '', $escape=false ) {
		
		$countries = $this->countries;
		
		if ( $countries ) foreach ( $countries as $key=>$value) :
			if ( $states =  $this->get_states($key) ) :
				echo '<optgroup label="'.$value.'">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="'.$key.':'.$state_key.'"';
    					
    					if ($selected_country==$key && $selected_state==$state_key) echo ' selected="selected"';
    					
    					echo '>'.$value.' &mdash; '. ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			else :
    			echo '<option';
    			if ($selected_country==$key && $selected_state=='*') echo ' selected="selected"';
    			echo ' value="'.$key.'">'. ($escape ? esc_js( __($value, 'wpdeals') ) : __($value, 'wpdeals') ) .'</option>';
			endif;
		endforeach;
	}
	
	/** Outputs the list of countries and states for use in multiselect boxes */
	function country_multiselect_options( $selected_countries = '', $escape=false ) {
		
		$countries = $this->countries;
		
		if ( $countries ) foreach ( $countries as $key=>$value) :
			if ( $states =  $this->get_states($key) ) :
				echo '<optgroup label="'.$value.'">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="'.$key.':'.$state_key.'"';
  
    					if (isset($selected_countries[$key]) && in_array($state_key, $selected_countries[$key])) echo ' selected="selected"';
    					
    					echo '>' . ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			else :
    			echo '<option';
    			
    			if (isset($selected_countries[$key]) && in_array('*', $selected_countries[$key])) echo ' selected="selected"';
    			
    			echo ' value="'.$key.'">'. ($escape ? esc_js( __($value, 'wpdeals') ) : __($value, 'wpdeals') ) .'</option>';
			endif;
		endforeach;
	}
}

