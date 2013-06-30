<?php


$current_dirname = dirname(__FILE__)."/";
require_once($current_dirname."KLogger.php");
require_once($current_dirname."config.php");
require_once($current_dirname."monit-graph.class.php");


$log = new KLogger($current_dirname."collect.log", KLogger::INFO );
$log->logInfo('Monit collector started');

if(!MonitGraph::checkConfig($server_configs)) 
    die('configuration error');
MonitGraph::setLog($log);

$data = file_get_contents('php://input');
$log->logInfo('Parsing Monit data');
$server_id = MonitGraph::parseXmlData("", 
                            $data, 
                            $chunk_size,
    					    $limit_number_of_chunks);
$log->logInfo('Monit collect done for ' . $server_id);
?>