( function ( $ ) {
	'use strict';
	/*	Initialize data  */

	const wowCountries = [
		{
			code: 'af',
			dial: '93',
			name: 'Afghanistan',
			pos: 48,
		},
		{
			code: 'ax',
			dial: '358',
			name: 'Åland Islands',
			pos: 224,
		},
		{
			code: 'al',
			dial: '355',
			name: 'Albania',
			pos: 96,
		},
		{
			code: 'dz',
			dial: '213',
			name: 'Algeria',
			pos: 960,
		},
		{
			code: 'as',
			dial: '1',
			name: 'American Samoa',
			pos: 160,
		},
		{
			code: 'ad',
			dial: '376',
			name: 'Andorra',
			pos: 16,
		},
		{
			code: 'ao',
			dial: '244',
			name: 'Angola',
			pos: 128,
		},
		{
			code: 'ai',
			dial: '1',
			name: 'Anguilla',
			pos: 80,
		},
		{
			code: 'ag',
			dial: '1',
			name: 'Antigua & Barbuda',
			pos: 64,
		},
		{
			code: 'ar',
			dial: '54',
			name: 'Argentina',
			pos: 144,
		},
		{
			code: 'am',
			dial: '374',
			name: 'Armenia',
			pos: 112,
		},
		{
			code: 'aw',
			dial: '297',
			name: 'Aruba',
			pos: 208,
		},
		{
			code: 'ac',
			dial: '247',
			name: 'Ascension Island',
			pos: 0,
		},
		{
			code: 'au',
			dial: '61',
			name: 'Australia',
			pos: 192,
		},
		{
			code: 'at',
			dial: '43',
			name: 'Austria',
			pos: 176,
		},
		{
			code: 'az',
			dial: '994',
			name: 'Azerbaijan',
			pos: 240,
		},
		{
			code: 'bs',
			dial: '1',
			name: 'Bahamas',
			pos: 496,
		},
		{
			code: 'bh',
			dial: '973',
			name: 'Bahrain',
			pos: 352,
		},
		{
			code: 'bd',
			dial: '880',
			name: 'Bangladesh',
			pos: 288,
		},
		{
			code: 'bb',
			dial: '1',
			name: 'Barbados',
			pos: 272,
		},
		{
			code: 'by',
			dial: '375',
			name: 'Belarus',
			pos: 544,
		},
		{
			code: 'be',
			dial: '32',
			name: 'Belgium',
			pos: 304,
		},
		{
			code: 'bz',
			dial: '501',
			name: 'Belize',
			pos: 560,
		},
		{
			code: 'bj',
			dial: '229',
			name: 'Benin',
			pos: 384,
		},
		{
			code: 'bm',
			dial: '1',
			name: 'Bermuda',
			pos: 416,
		},
		{
			code: 'bt',
			dial: '975',
			name: 'Bhutan',
			pos: 512,
		},
		{
			code: 'bo',
			dial: '591',
			name: 'Bolivia',
			pos: 448,
		},
		{
			code: 'ba',
			dial: '387',
			name: 'Bosnia & Herzegovina',
			pos: 256,
		},
		{
			code: 'bw',
			dial: '267',
			name: 'Botswana',
			pos: 528,
		},
		{
			code: 'br',
			dial: '55',
			name: 'Brazil',
			pos: 480,
		},
		{
			code: 'io',
			dial: '246',
			name: 'British Indian Ocean Territory',
			pos: 1632,
		},
		{
			code: 'vg',
			dial: '1',
			name: 'British Virgin Islands',
			pos: 3712,
		},
		{
			code: 'bn',
			dial: '673',
			name: 'Brunei',
			pos: 432,
		},
		{
			code: 'bg',
			dial: '359',
			name: 'Bulgaria',
			pos: 336,
		},
		{
			code: 'bf',
			dial: '226',
			name: 'Burkina Faso',
			pos: 320,
		},
		{
			code: 'bi',
			dial: '257',
			name: 'Burundi',
			pos: 368,
		},
		{
			code: 'kh',
			dial: '855',
			name: 'Cambodia',
			pos: 1808,
		},
		{
			code: 'cm',
			dial: '237',
			name: 'Cameroon',
			pos: 720,
		},
		{
			code: 'ca',
			dial: '1',
			name: 'Canada',
			pos: 576,
		},
		{
			code: 'cv',
			dial: '238',
			name: 'Cape Verde',
			pos: 800,
		},
		{
			code: 'bq',
			dial: '599',
			name: 'Caribbean Netherlands',
			pos: 464,
		},
		{
			code: 'ky',
			dial: '1',
			name: 'Cayman Islands',
			pos: 1920,
		},
		{
			code: 'cf',
			dial: '236',
			name: 'Central African Republic',
			pos: 624,
		},
		{
			code: 'td',
			dial: '235',
			name: 'Chad',
			pos: 3360,
		},
		{
			code: 'cl',
			dial: '56',
			name: 'Chile',
			pos: 704,
		},
		{
			code: 'cn',
			dial: '86',
			name: 'China',
			pos: 736,
		},
		{
			code: 'cx',
			dial: '61',
			name: 'Christmas Island',
			pos: 832,
		},
		{
			code: 'cc',
			dial: '61',
			name: 'Cocos (Keeling) Islands',
			pos: 592,
		},
		{
			code: 'co',
			dial: '57',
			name: 'Colombia',
			pos: 752,
		},
		{
			code: 'km',
			dial: '269',
			name: 'Comoros',
			pos: 1840,
		},
		{
			code: 'cg',
			dial: '242',
			name: 'Congo - Brazzaville',
			pos: 640,
		},
		{
			code: 'cd',
			dial: '243',
			name: 'Congo - Kinshasa',
			pos: 608,
		},
		{
			code: 'ck',
			dial: '682',
			name: 'Cook Islands',
			pos: 688,
		},
		{
			code: 'cr',
			dial: '506',
			name: 'Costa Rica',
			pos: 768,
		},
		{
			code: 'ci',
			dial: '225',
			name: 'Côte d’Ivoire',
			pos: 672,
		},
		{
			code: 'hr',
			dial: '385',
			name: 'Croatia',
			pos: 1504,
		},
		{
			code: 'cu',
			dial: '53',
			name: 'Cuba',
			pos: 784,
		},
		{
			code: 'cw',
			dial: '599',
			name: 'Curaçao',
			pos: 816,
		},
		{
			code: 'cy',
			dial: '357',
			name: 'Cyprus',
			pos: 848,
		},
		{
			code: 'cz',
			dial: '420',
			name: 'Czechia',
			pos: 864,
		},
		{
			code: 'dk',
			dial: '45',
			name: 'Denmark',
			pos: 912,
		},
		{
			code: 'dj',
			dial: '253',
			name: 'Djibouti',
			pos: 896,
		},
		{
			code: 'dm',
			dial: '1',
			name: 'Dominica',
			pos: 928,
		},
		{
			code: 'do',
			dial: '1',
			name: 'Dominican Republic',
			pos: 944,
		},
		{
			code: 'ec',
			dial: '593',
			name: 'Ecuador',
			pos: 976,
		},
		{
			code: 'eg',
			dial: '20',
			name: 'Egypt',
			pos: 1008,
		},
		{
			code: 'sv',
			dial: '503',
			name: 'El Salvador',
			pos: 3280,
		},
		{
			code: 'gq',
			dial: '240',
			name: 'Equatorial Guinea',
			pos: 1376,
		},
		{
			code: 'er',
			dial: '291',
			name: 'Eritrea',
			pos: 1040,
		},
		{
			code: 'ee',
			dial: '372',
			name: 'Estonia',
			pos: 992,
		},
		{
			code: 'sz',
			dial: '268',
			name: 'Eswatini',
			pos: 3328,
		},
		{
			code: 'et',
			dial: '251',
			name: 'Ethiopia',
			pos: 1072,
		},
		{
			code: 'fk',
			dial: '500',
			name: 'Falkland Islands',
			pos: 1120,
		},
		{
			code: 'fo',
			dial: '298',
			name: 'Faroe Islands',
			pos: 1152,
		},
		{
			code: 'fj',
			dial: '679',
			name: 'Fiji',
			pos: 1104,
		},
		{
			code: 'fi',
			dial: '358',
			name: 'Finland',
			pos: 1088,
		},
		{
			code: 'fr',
			dial: '33',
			name: 'France',
			pos: 1168,
		},
		{
			code: 'gf',
			dial: '594',
			name: 'French Guiana',
			pos: 1248,
		},
		{
			code: 'pf',
			dial: '689',
			name: 'French Polynesia',
			pos: 2736,
		},
		{
			code: 'ga',
			dial: '241',
			name: 'Gabon',
			pos: 1184,
		},
		{
			code: 'gm',
			dial: '220',
			name: 'Gambia',
			pos: 1328,
		},
		{
			code: 'ge',
			dial: '995',
			name: 'Georgia',
			pos: 1232,
		},
		{
			code: 'de',
			dial: '49',
			name: 'Germany',
			pos: 880,
		},
		{
			code: 'gh',
			dial: '233',
			name: 'Ghana',
			pos: 1280,
		},
		{
			code: 'gi',
			dial: '350',
			name: 'Gibraltar',
			pos: 1296,
		},
		{
			code: 'gr',
			dial: '30',
			name: 'Greece',
			pos: 1392,
		},
		{
			code: 'gl',
			dial: '299',
			name: 'Greenland',
			pos: 1312,
		},
		{
			code: 'gd',
			dial: '1',
			name: 'Grenada',
			pos: 1216,
		},
		{
			code: 'gp',
			dial: '590',
			name: 'Guadeloupe',
			pos: 1360,
		},
		{
			code: 'gu',
			dial: '1',
			name: 'Guam',
			pos: 1424,
		},
		{
			code: 'gt',
			dial: '502',
			name: 'Guatemala',
			pos: 1408,
		},
		{
			code: 'gg',
			dial: '44',
			name: 'Guernsey',
			pos: 1264,
		},
		{
			code: 'gn',
			dial: '224',
			name: 'Guinea',
			pos: 1344,
		},
		{
			code: 'gw',
			dial: '245',
			name: 'Guinea-Bissau',
			pos: 1440,
		},
		{
			code: 'gy',
			dial: '592',
			name: 'Guyana',
			pos: 1456,
		},
		{
			code: 'ht',
			dial: '509',
			name: 'Haiti',
			pos: 1520,
		},
		{
			code: 'hn',
			dial: '504',
			name: 'Honduras',
			pos: 1488,
		},
		{
			code: 'hk',
			dial: '852',
			name: 'Hong Kong SAR China',
			pos: 1472,
		},
		{
			code: 'hu',
			dial: '36',
			name: 'Hungary',
			pos: 1536,
		},
		{
			code: 'is',
			dial: '354',
			name: 'Iceland',
			pos: 1680,
		},
		{
			code: 'in',
			dial: '91',
			name: 'India',
			pos: 1616,
		},
		{
			code: 'id',
			dial: '62',
			name: 'Indonesia',
			pos: 1552,
		},
		{
			code: 'ir',
			dial: '98',
			name: 'Iran',
			pos: 1664,
		},
		{
			code: 'iq',
			dial: '964',
			name: 'Iraq',
			pos: 1648,
		},
		{
			code: 'ie',
			dial: '353',
			name: 'Ireland',
			pos: 1568,
		},
		{
			code: 'im',
			dial: '44',
			name: 'Isle of Man',
			pos: 1600,
		},
		{
			code: 'il',
			dial: '972',
			name: 'Israel',
			pos: 1584,
		},
		{
			code: 'it',
			dial: '39',
			name: 'Italy',
			pos: 1696,
		},
		{
			code: 'jm',
			dial: '1',
			name: 'Jamaica',
			pos: 1728,
		},
		{
			code: 'jp',
			dial: '81',
			name: 'Japan',
			pos: 1760,
		},
		{
			code: 'je',
			dial: '44',
			name: 'Jersey',
			pos: 1712,
		},
		{
			code: 'jo',
			dial: '962',
			name: 'Jordan',
			pos: 1744,
		},
		{
			code: 'kz',
			dial: '7',
			name: 'Kazakhstan',
			pos: 1936,
		},
		{
			code: 'ke',
			dial: '254',
			name: 'Kenya',
			pos: 1776,
		},
		{
			code: 'ki',
			dial: '686',
			name: 'Kiribati',
			pos: 1824,
		},
		{
			code: 'xk',
			dial: '383',
			name: 'Kosovo',
			pos: 3808,
		},
		{
			code: 'kw',
			dial: '965',
			name: 'Kuwait',
			pos: 1904,
		},
		{
			code: 'kg',
			dial: '996',
			name: 'Kyrgyzstan',
			pos: 1792,
		},
		{
			code: 'la',
			dial: '856',
			name: 'Laos',
			pos: 1952,
		},
		{
			code: 'lv',
			dial: '371',
			name: 'Latvia',
			pos: 2096,
		},
		{
			code: 'lb',
			dial: '961',
			name: 'Lebanon',
			pos: 1968,
		},
		{
			code: 'ls',
			dial: '266',
			name: 'Lesotho',
			pos: 2048,
		},
		{
			code: 'lr',
			dial: '231',
			name: 'Liberia',
			pos: 2032,
		},
		{
			code: 'ly',
			dial: '218',
			name: 'Libya',
			pos: 2112,
		},
		{
			code: 'li',
			dial: '423',
			name: 'Liechtenstein',
			pos: 2000,
		},
		{
			code: 'lt',
			dial: '370',
			name: 'Lithuania',
			pos: 2064,
		},
		{
			code: 'lu',
			dial: '352',
			name: 'Luxembourg',
			pos: 2080,
		},
		{
			code: 'mo',
			dial: '853',
			name: 'Macao SAR China',
			pos: 2304,
		},
		{
			code: 'mg',
			dial: '261',
			name: 'Madagascar',
			pos: 2208,
		},
		{
			code: 'mw',
			dial: '265',
			name: 'Malawi',
			pos: 2432,
		},
		{
			code: 'my',
			dial: '60',
			name: 'Malaysia',
			pos: 2464,
		},
		{
			code: 'mv',
			dial: '960',
			name: 'Maldives',
			pos: 2416,
		},
		{
			code: 'ml',
			dial: '223',
			name: 'Mali',
			pos: 2256,
		},
		{
			code: 'mt',
			dial: '356',
			name: 'Malta',
			pos: 2384,
		},
		{
			code: 'mh',
			dial: '692',
			name: 'Marshall Islands',
			pos: 2224,
		},
		{
			code: 'mq',
			dial: '596',
			name: 'Martinique',
			pos: 2336,
		},
		{
			code: 'mr',
			dial: '222',
			name: 'Mauritania',
			pos: 2352,
		},
		{
			code: 'mu',
			dial: '230',
			name: 'Mauritius',
			pos: 2400,
		},
		{
			code: 'yt',
			dial: '262',
			name: 'Mayotte',
			pos: 3840,
		},
		{
			code: 'mx',
			dial: '52',
			name: 'Mexico',
			pos: 2448,
		},
		{
			code: 'fm',
			dial: '691',
			name: 'Micronesia',
			pos: 1136,
		},
		{
			code: 'md',
			dial: '373',
			name: 'Moldova',
			pos: 2160,
		},
		{
			code: 'mc',
			dial: '377',
			name: 'Monaco',
			pos: 2144,
		},
		{
			code: 'mn',
			dial: '976',
			name: 'Mongolia',
			pos: 2288,
		},
		{
			code: 'me',
			dial: '382',
			name: 'Montenegro',
			pos: 2176,
		},
		{
			code: 'ms',
			dial: '1',
			name: 'Montserrat',
			pos: 2368,
		},
		{
			code: 'ma',
			dial: '212',
			name: 'Morocco',
			pos: 2128,
		},
		{
			code: 'mz',
			dial: '258',
			name: 'Mozambique',
			pos: 2480,
		},
		{
			code: 'mm',
			dial: '95',
			name: 'Myanmar (Burma)',
			pos: 2272,
		},
		{
			code: 'na',
			dial: '264',
			name: 'Namibia',
			pos: 2496,
		},
		{
			code: 'nr',
			dial: '674',
			name: 'Nauru',
			pos: 2640,
		},
		{
			code: 'np',
			dial: '977',
			name: 'Nepal',
			pos: 2624,
		},
		{
			code: 'nl',
			dial: '31',
			name: 'Netherlands',
			pos: 2592,
		},
		{
			code: 'nc',
			dial: '687',
			name: 'New Caledonia',
			pos: 2512,
		},
		{
			code: 'nz',
			dial: '64',
			name: 'New Zealand',
			pos: 2672,
		},
		{
			code: 'ni',
			dial: '505',
			name: 'Nicaragua',
			pos: 2576,
		},
		{
			code: 'ne',
			dial: '227',
			name: 'Niger',
			pos: 2528,
		},
		{
			code: 'ng',
			dial: '234',
			name: 'Nigeria',
			pos: 2560,
		},
		{
			code: 'nu',
			dial: '683',
			name: 'Niue',
			pos: 2656,
		},
		{
			code: 'nf',
			dial: '672',
			name: 'Norfolk Island',
			pos: 2544,
		},
		{
			code: 'kp',
			dial: '850',
			name: 'North Korea',
			pos: 1872,
		},
		{
			code: 'mk',
			dial: '389',
			name: 'North Macedonia',
			pos: 2240,
		},
		{
			code: 'mp',
			dial: '1',
			name: 'Northern Mariana Islands',
			pos: 2320,
		},
		{
			code: 'no',
			dial: '47',
			name: 'Norway',
			pos: 2608,
		},
		{
			code: 'om',
			dial: '968',
			name: 'Oman',
			pos: 2688,
		},
		{
			code: 'pk',
			dial: '92',
			name: 'Pakistan',
			pos: 2784,
		},
		{
			code: 'pw',
			dial: '680',
			name: 'Palau',
			pos: 2880,
		},
		{
			code: 'ps',
			dial: '970',
			name: 'Palestinian Territories',
			pos: 2848,
		},
		{
			code: 'pa',
			dial: '507',
			name: 'Panama',
			pos: 2704,
		},
		{
			code: 'pg',
			dial: '675',
			name: 'Papua New Guinea',
			pos: 2752,
		},
		{
			code: 'py',
			dial: '595',
			name: 'Paraguay',
			pos: 2896,
		},
		{
			code: 'pe',
			dial: '51',
			name: 'Peru',
			pos: 2720,
		},
		{
			code: 'ph',
			dial: '63',
			name: 'Philippines',
			pos: 2768,
		},
		{
			code: 'pl',
			dial: '48',
			name: 'Poland',
			pos: 2800,
		},
		{
			code: 'pt',
			dial: '351',
			name: 'Portugal',
			pos: 2864,
		},
		{
			code: 'pr',
			dial: '1',
			name: 'Puerto Rico',
			pos: 2832,
		},
		{
			code: 'qa',
			dial: '974',
			name: 'Qatar',
			pos: 2912,
		},
		{
			code: 're',
			dial: '262',
			name: 'Réunion',
			pos: 2928,
		},
		{
			code: 'ro',
			dial: '40',
			name: 'Romania',
			pos: 2944,
		},
		{
			code: 'ru',
			dial: '7',
			name: 'Russia',
			pos: 2976,
		},
		{
			code: 'rw',
			dial: '250',
			name: 'Rwanda',
			pos: 2992,
		},
		{
			code: 'ws',
			dial: '685',
			name: 'Samoa',
			pos: 3792,
		},
		{
			code: 'sm',
			dial: '378',
			name: 'San Marino',
			pos: 3184,
		},
		{
			code: 'st',
			dial: '239',
			name: 'São Tomé & Príncipe',
			pos: 3264,
		},
		{
			code: 'sa',
			dial: '966',
			name: 'Saudi Arabia',
			pos: 3008,
		},
		{
			code: 'sn',
			dial: '221',
			name: 'Senegal',
			pos: 3200,
		},
		{
			code: 'rs',
			dial: '381',
			name: 'Serbia',
			pos: 2960,
		},
		{
			code: 'sc',
			dial: '248',
			name: 'Seychelles',
			pos: 3040,
		},
		{
			code: 'sl',
			dial: '232',
			name: 'Sierra Leone',
			pos: 3168,
		},
		{
			code: 'sg',
			dial: '65',
			name: 'Singapore',
			pos: 3088,
		},
		{
			code: 'sx',
			dial: '1',
			name: 'Sint Maarten',
			pos: 3296,
		},
		{
			code: 'sk',
			dial: '421',
			name: 'Slovakia',
			pos: 3152,
		},
		{
			code: 'si',
			dial: '386',
			name: 'Slovenia',
			pos: 3120,
		},
		{
			code: 'sb',
			dial: '677',
			name: 'Solomon Islands',
			pos: 3024,
		},
		{
			code: 'so',
			dial: '252',
			name: 'Somalia',
			pos: 3216,
		},
		{
			code: 'za',
			dial: '27',
			name: 'South Africa',
			pos: 3856,
		},
		{
			code: 'kr',
			dial: '82',
			name: 'South Korea',
			pos: 1888,
		},
		{
			code: 'ss',
			dial: '211',
			name: 'South Sudan',
			pos: 3248,
		},
		{
			code: 'es',
			dial: '34',
			name: 'Spain',
			pos: 1056,
		},
		{
			code: 'lk',
			dial: '94',
			name: 'Sri Lanka',
			pos: 2016,
		},
		{
			code: 'bl',
			dial: '590',
			name: 'St. Barthélemy',
			pos: 400,
		},
		{
			code: 'sh',
			dial: '290',
			name: 'St. Helena',
			pos: 3104,
		},
		{
			code: 'kn',
			dial: '1',
			name: 'St. Kitts & Nevis',
			pos: 1856,
		},
		{
			code: 'lc',
			dial: '1',
			name: 'St. Lucia',
			pos: 1984,
		},
		{
			code: 'mf',
			dial: '590',
			name: 'St. Martin',
			pos: 2192,
		},
		{
			code: 'pm',
			dial: '508',
			name: 'St. Pierre & Miquelon',
			pos: 2816,
		},
		{
			code: 'vc',
			dial: '1',
			name: 'St. Vincent & Grenadines',
			pos: 3680,
		},
		{
			code: 'sd',
			dial: '249',
			name: 'Sudan',
			pos: 3056,
		},
		{
			code: 'sr',
			dial: '597',
			name: 'Suriname',
			pos: 3232,
		},
		{
			code: 'sj',
			dial: '47',
			name: 'Svalbard & Jan Mayen',
			pos: 3136,
		},
		{
			code: 'se',
			dial: '46',
			name: 'Sweden',
			pos: 3072,
		},
		{
			code: 'ch',
			dial: '41',
			name: 'Switzerland',
			pos: 656,
		},
		{
			code: 'sy',
			dial: '963',
			name: 'Syria',
			pos: 3312,
		},
		{
			code: 'tw',
			dial: '886',
			name: 'Taiwan',
			pos: 3552,
		},
		{
			code: 'tj',
			dial: '992',
			name: 'Tajikistan',
			pos: 3408,
		},
		{
			code: 'tz',
			dial: '255',
			name: 'Tanzania',
			pos: 3568,
		},
		{
			code: 'th',
			dial: '66',
			name: 'Thailand',
			pos: 3392,
		},
		{
			code: 'tl',
			dial: '670',
			name: 'Timor-Leste',
			pos: 3440,
		},
		{
			code: 'tg',
			dial: '228',
			name: 'Togo',
			pos: 3376,
		},
		{
			code: 'tk',
			dial: '690',
			name: 'Tokelau',
			pos: 3424,
		},
		{
			code: 'to',
			dial: '676',
			name: 'Tonga',
			pos: 3488,
		},
		{
			code: 'tt',
			dial: '1',
			name: 'Trinidad & Tobago',
			pos: 3520,
		},
		{
			code: 'tn',
			dial: '216',
			name: 'Tunisia',
			pos: 3472,
		},
		{
			code: 'tr',
			dial: '90',
			name: 'Turkey',
			pos: 3504,
		},
		{
			code: 'tm',
			dial: '993',
			name: 'Turkmenistan',
			pos: 3456,
		},
		{
			code: 'tc',
			dial: '1',
			name: 'Turks & Caicos Islands',
			pos: 3344,
		},
		{
			code: 'tv',
			dial: '688',
			name: 'Tuvalu',
			pos: 3536,
		},
		{
			code: 'vi',
			dial: '1',
			name: 'U.S. Virgin Islands',
			pos: 3728,
		},
		{
			code: 'ug',
			dial: '256',
			name: 'Uganda',
			pos: 3600,
		},
		{
			code: 'ua',
			dial: '380',
			name: 'Ukraine',
			pos: 3584,
		},
		{
			code: 'ae',
			dial: '971',
			name: 'United Arab Emirates',
			pos: 32,
		},
		{
			code: 'gb',
			dial: '44',
			name: 'United Kingdom',
			pos: 1200,
		},
		{
			code: 'us',
			dial: '1',
			name: 'United States',
			pos: 3616,
		},
		{
			code: 'uy',
			dial: '598',
			name: 'Uruguay',
			pos: 3632,
		},
		{
			code: 'uz',
			dial: '998',
			name: 'Uzbekistan',
			pos: 3648,
		},
		{
			code: 'vu',
			dial: '678',
			name: 'Vanuatu',
			pos: 3760,
		},
		{
			code: 'va',
			dial: '39',
			name: 'Vatican City',
			pos: 3664,
		},
		{
			code: 've',
			dial: '58',
			name: 'Venezuela',
			pos: 3696,
		},
		{
			code: 'vn',
			dial: '84',
			name: 'Vietnam',
			pos: 3744,
		},
		{
			code: 'wf',
			dial: '681',
			name: 'Wallis & Futuna',
			pos: 3776,
		},
		{
			code: 'eh',
			dial: '212',
			name: 'Western Sahara',
			pos: 1024,
		},
		{
			code: 'ye',
			dial: '967',
			name: 'Yemen',
			pos: 3824,
		},
		{
			code: 'zm',
			dial: '260',
			name: 'Zambia',
			pos: 3872,
		},
		{
			code: 'zw',
			dial: '263',
			name: 'Zimbabwe',
			pos: 3888,
		},
	];

	$( '.prad-tel-container' ).each( function () {
		const $wrapper = $( this );
		const $dropdown = $wrapper.find( '.prad-tel-country-list-container' );
		const $selected = $wrapper.find( '.prad-tel-country-wrapper' );
		const $list = $wrapper.find( '.prad-country-list' );
		const $flag = $wrapper.find( '.prad-flag-selected' );
		const $dial = $wrapper.find( '.prad-dial-code-show' );
		const $searchInput = $wrapper.find( '.prad-country-search-input' );

		function renderList( search ) {
			$list.empty();
			const dialSelected = $dial.attr( 'data-selected' );
			wowCountries.forEach( ( item, index ) => {
				if (
					item.name.toLowerCase().includes( search ) ||
					item.code.toLowerCase().includes( search )
				) {
					const $item = $( `
						<div class="prad-country-item ${
							dialSelected === item.code
								? 'prad-country-item-selected'
								: ''
						}" data-index="${ index }">
							<div class="prad-tel-flag" style="background-position: -${
								item.pos
							}px 0;"></div>
							<span>${ item.name }</span>
							<span class="prad-dial-code">+${ item.dial }</span>
						</div>
					` );
					$list.append( $item );
				}
			} );
		}

		renderList( '' );

		$selected.on( 'click', function ( e ) {
			e.stopPropagation();
			const isOpen = $dropdown.hasClass( 'prad-flag-drop-visible' );

			// Close all other dropdowns
			$( '.prad-tel-country-list-container' )
				.removeClass( 'prad-flag-drop-visible' )
				.addClass( 'prad-flag-drop-hidden' );

			if ( ! isOpen ) {
				$dropdown
					.removeClass( 'prad-flag-drop-hidden' )
					.addClass( 'prad-flag-drop-visible' );
				$searchInput.val( '' ).trigger( 'input' ).focus();
			}
			$wrapper
				.find( '.prad-flag-arrow' )
				.toggleClass( 'prad-flag-arrow-rotated', ! isOpen );
		} );

		$list.on( 'click', '.prad-country-item', function ( e ) {
			e.stopPropagation();
			const index = $( this ).data( 'index' );
			const item = wowCountries[ index ];
			$flag.css( 'background-position', '-' + item.pos + 'px 0' );
			$dial.text( '+' + item.dial );
			$dial.attr( 'data-selected', item.code );
			$dropdown
				.removeClass( 'prad-flag-drop-visible' )
				.addClass( 'prad-flag-drop-hidden' );
			$wrapper
				.find( '.prad-flag-arrow' )
				.removeClass( 'prad-flag-arrow-rotated' );
			$( '.prad-country-item' ).removeClass(
				'prad-country-item-selected'
			);
			$( this ).addClass( 'prad-country-item-selected' );
		} );

		$searchInput.on( 'input', function () {
			const search = $( this ).val().toLowerCase();
			renderList( search );
		} );
	} );
	$( '.prad-tel-country-list-container' ).on( 'click', function ( e ) {
		e.stopPropagation();
	} );

	// Close dropdowns when clicking outside
	$( document ).on( 'click', function ( e ) {
		if ( ! $( e.target ).closest( '.prad-tel-container' ).length ) {
			$( '.prad-tel-country-list-container' )
				.removeClass( 'prad-flag-drop-visible' )
				.addClass( 'prad-flag-drop-hidden' );
		}
	} );

	// eslint-disable-next-line no-undef
} )( jQuery );
