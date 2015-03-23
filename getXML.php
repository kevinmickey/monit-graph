<?php
/**
 * Monit Graph
 *
 * Copyright (c) 2011, Dan Schultzer <http://abcel-online.com/>
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Dan Schultzer nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL DAN SCHULTZER BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package monit-graph
 * @author Dan Schultzer <http://abcel-online.com/>
 * @copyright Dan Schultzer
 */
	$current_dirname = dirname(__FILE__)."/";
 	require_once($current_dirname."config.php");
	require_once($current_dirname."monit-graph.class.php");
	require_once($current_dirname."KLogger.php");
	$log = new KLogger($current_dirname."index.log", KLogger::INFO );
	MonitGraph::setLog($log);


	if(isset($_GET['servers'])) {
		if($dynamic == false) {
			if(!MonitGraph::checkConfig($server_configs)) 
				die("Fatal error. Check the error log please."); // If configs are not good we quit
		} else {
			//build dynamic config
			$server_configs = array();
			$datadir = $current_dirname."data";
			$datah = opendir($datadir);
			while (false !== ($filename = readdir($datah))) {
				if (is_file($datadir."/".$filename)) {
					$log->logInfo($filename . ' found');
	
					if (($server_name = strstr($filename, "-server.xml", true))) {
						$log->logInfo($filename . ' match');
						$server["name"] = $server_name;
						$server["server_id"] = $server_name;
						$server_configs[] = $server;
					}
				}
			}
			sort($server_configs);
		echo json_encode($server_configs);
		}
	return;
	}

	/* Get individuel server stats */
	if(isset($_GET['server_id']) && strlen($_GET['server_id'])>0){
		/* Variables */
		$output_body = "";
		$_SELECTED = array();

		/* Time Range */
		if(isset($_GET['time_range'])){
			$time_range = intVal($_GET['time_range']);
			$_SELECTED[$time_range]=' selected="selected"';
		}else{
			$time_range = $default_time_range;
			$_SELECTED[$time_range]=' selected="selected"';
		}
	
		/* Specific services */
		if(isset($_GET['specific_services'])) 
			$specific_services = (string)$_GET['specific_services'];
		else 
			$specific_services = $default_specific_service;
	
		/* Iterate all json files in data directory */
		$i = 0;
		$files = MonitGraph::getLogFilesForServerID($_GET['server_id'],$specific_services);
		$services = array();
		foreach($files as $file){
			$filename = basename($file);
			$simpleXml = simplexml_load_file($file);
			$services[] = json_encode($simpleXml);
		}
		echo json_encode($services);
	return;
	}
?>
