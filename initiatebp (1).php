<?
writetolog($_REQUEST, 'new request');	
$cnt = "CONTACT_".$_REQUEST['cnt'];

/* AUTH */
    $domain            = 'crmvangogvomne.bitrix24.ru'; 
    $auth              = 'ax0wnj10jrxezphv';
    $user              = '16'; 
	$template		   = '76';

	$bpcheck = executeREST(
            'bizproc.workflow.instances',
            array(
                        'SELECT' => array(   
							'ID', 
							'MODIFIED', 
							'OWNED_UNTIL', 
							'MODULE_ID', 
							'ENTITY', 
							'DOCUMENT_ID', 
							'STARTED', 
							'STARTED_BY', 
							'TEMPLATE_ID'
                            ),
						'ORDER' => array(
							'STARTED' => DESC
							),
                         'FILTER' => array(
							'ENTITY' => 'CCrmDocumentContact',
                    		'DOCUMENT_ID' => $cnt,
                  			'TEMPLATE_ID' => $template
                            ),
                    ),
            $domain, $auth, $user);

			if ($bpcheck['result'][1]) {
				$bpclose = executeREST(
            		'bizproc.workflow.terminate',
            		array(
						'ID' => $bpcheck['result'][1]['ID']
           			 ),
           		 $domain, $auth, $user);
				writetolog($bpclose, 'bp terminated');	
			}

			





writetolog($bpcheck, 'bpfound');   
	

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
    file_put_contents(getcwd() . '/bizprocinitiate.log', $log, FILE_APPEND);
    return true;
}

