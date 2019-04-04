<?
writetolog($_REQUEST, 'new request');	
$wfid = $_REQUEST['wfid'];
$cnt = $_REQUEST['cnt'];

/* AUTH */
    $domain            = 'crmvangogvomne.bitrix24.ru'; 
    $auth              = 'ax0wnj10jrxezphv';
    $user              = '16'; 

	$dealcheck = executeREST(
            'crm.deal.list',
            array(
                        'ORDER' => array(
                                'ID' => 'DESC'
                            ),
                            'FILTER' => array(
                                'CONTACT_ID' => $cnt, 
								'!STAGE_ID' => 'WON'
                            ),
                            'SELECT' => array(
                                '*'
                            ),
                    ),
            $domain, $auth, $user);

	if ($dealcheck['result']) {
		$bpclose = executeREST(
            'bizproc.workflow.terminate',
            array(
				'ID' => $wfid
            ),
            $domain, $auth, $user);
		writetolog($bpclose, 'bp terminated');	

	}

function executeREST ($method, array $params, $domain, $auth, $user) {
            $queryUrl = 'https://'.$domain.'/rest/'.$user.'/'.$auth.'/'.$method.'.json';
            $queryData = http_build_query($params);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $queryUrl,
                CURLOPT_POSTFIELDS => $queryData,
            ));
            return json_decode(curl_exec($curl), true);
            curl_close($curl);
}


function writeToLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/bizprocterm.log', $log, FILE_APPEND);
    return true;
}

