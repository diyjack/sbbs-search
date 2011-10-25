<?php
/**
 * suggest.php 
 * SBBS ��ȡ��������(���JSON)
 * 
 * ���ļ��� xunsearch PHP-SDK �����Զ����ɣ������ʵ����������޸�
 * ����ʱ�䣺2011-10-24 20:11:27
 */
// ���� XS ����ļ�
require_once '/home/bbsweb/html/search/lib/XS.php';

// Prefix Query is: term (by jQuery-ui)
$q = isset($_GET['term']) ? trim($_GET['term']) : '';
$q = get_magic_quotes_gpc() ? stripslashes($q) : $q;
$terms = array();
if (!empty($q) && strpos($q, ':') === false)
{
	try
	{
		$xs = new XS('sbbs');
		$terms = $xs->search->setCharset('UTF-8')->getExpandedQuery($q);
	}
	catch (XSException $e)
	{
		
	}
}

// output json
header("Content-Type: application/json; charset=utf-8");
echo json_encode($terms);
exit(0);
