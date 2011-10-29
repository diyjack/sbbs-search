<?php
require_once 'search/lib/XS.php';
require_once 'init.php';

Lib::load(array('utils/pagination.php'));

// ֧�ֵ� GET �����б�
// q: ��ѯ���
// m: ����ģ����������ֵΪ yes/no
// f: ֻ����ĳ���ֶΣ���ֵΪ�ֶ����ƣ�Ҫ����ֶε�������ʽΪ self/both
// s: �����ֶ����Ƽ���ʽ����ֵ��ʽΪ��xxx_ASC �� xxx_DESC
// p: ��ʾ�ڼ�ҳ��ÿҳ����Ϊ XSSearch::PAGE_SIZE �� 10 ��
// ie: ��ѯ�����룬Ĭ��Ϊ GBK
// oe: ������룬Ĭ��Ϊ GBK
// xml: �Ƿ���������� XML ��ʽ�������ֵΪ yes/no
//
// variables
$eu = '';
$__ = array('q', 'm', 'f', 's', 'p', 'ie', 'oe', 'xml');
foreach ($__ as $_)
    $$_ = isset($_GET[$_]) ? $_GET[$_] : '';

// input encoding
if (!empty($ie) && !empty($q) && strcasecmp($ie, 'GBK'))
{
    $q = XS::convert($q, $cs, $ie);
    $eu .= '&ie=' . $ie;
}

// output encoding
if (!empty($oe) && strcasecmp($oe, 'GBK'))
{

    function xs_output_encoding($buf)
    {
        return XS::convert($buf, $GLOBALS['oe'], 'GBK');
    }
    ob_start('xs_output_encoding');
    $eu .= '&oe=' . $oe;
}
else
{
    $oe = 'GBK';
}

// recheck request parameters
$q = get_magic_quotes_gpc() ? stripslashes($q) : $q;

// base url
$bu = '/s?q=' . urlencode($_GET['q']) . $eu;

// other variable maybe used in tpl
$count = $total = $search_cost = 0;
$docs = $related = $corrected = $hot = array();
$error = $pager = '';
$total_begin = microtime(true);

// perform the search
try
{
    $xs = new XS('sbbs');
    $search = $xs->search;
    $search->setCharset('GBK');

    if (empty($q))
    {
        // just show hot query
        $hot = $search->getHotQuery(10);
    }
    else
    {
        // fuzzy search
        $search->setFuzzy(false);

        // set query
        $search->setQuery($q);

        // filter private posts
        $search->addRange('access', 1, 1);

        // set offset, limit
        $p = max(1, intval($p));
        $n = 10;
        $search->setLimit($n, ($p - 1) * $n);

        // get the result
        $search_begin = microtime(true);
        $docs = $search->search();
        $search_cost = microtime(true) - $search_begin;

        // get other result
        $count = $search->getLastCount();
        $total = $search->getDbTotal();

        // try to corrected, if resul too few
        if ($count < 1 || $count < ceil(0.001 * $total))
            $corrected = $search->getCorrectedQuery();
        // get related query
        $related = $search->getRelatedQuery();
    }
}
catch (XSException $e)
{
    $error = strval($e);
}

// calculate total time cost
$total_cost = microtime(true) - $total_begin;

if ($count > $n)
    $pager = paginate_three($bu, $p, (int)($count / $n) + 1, 4);

// output the data
include dirname(__FILE__) . '/search.html';
?>
