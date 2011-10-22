<?php
/**
 * suggest.php 
 * Sbbs ��ȡ��������(���JSON)
 * 
 * ���ļ��� xunsearch PHP-SDK �����Զ����ɣ������ʵ����������޸�
 * ����ʱ�䣺2011-10-22 23:44:29
 */
// ���� XS ����ļ�
require_once dirname(__FILE__) . '/lib/XS.php';

// Prefix Query is: term (by jQuery-ui)
$q = isset($_GET['term']) ? trim($_GET['term']) : '';
$q = get_magic_quotes_gpc() ? stripslashes($q) : $q;
$terms = array();
if (!empty($q) && strpos($q, ':') === false)
{
	try
	{
		$xs = new XS(dirname(__FILE__) . '/app/sbbs.ini');
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
