<?php
namespace TLSP;
require __DIR__ . \DIRECTORY_SEPARATOR . 'autoload.php';

use TLSP\Storage\Products;
use TLSP\Storage\User;
use TLSP\Storage\Queue as SQueue;
use TLSP\Storage\Rules;
use TLSP\Storage\Rule;
use TLSP\Storage\Product;

define('TLSP_ROOT', __DIR__. \DIRECTORY_SEPARATOR);
define('TLSP_CONFIG_DIR', TLSP_ROOT. 'config'. \DIRECTORY_SEPARATOR . 'rule.php');

class TLSP
{
	private $cart;
	private $products;
	private $order;
	
	private $user;
	
	private $ruleTpl = array(
			'startTime' => 0,
			'stopTime' => 0,
			'limitPerUser' => 0,
			'stock' => 0,
			);
	
	private $rules = array();
	
	public function __construct(User $user)
	{
		$this->user = $user;
		$this->cart = new Cart($user->id);
		$this->products =  new Products();
		$this->initRule();
		//$this->order = $order ? $order : new Order();
	}
	
	protected function initRule()
	{
		
	}

	public function productsToRules($pros, $ruleID)
    {
        if (empty($pros) || empty($ruleID))
        {
            throw new \Exception('Invalid product id and rule id.');
        }
        else
       {
            $rules = Rules::singleton();

            if (!$rules->pushProduct($ruleID, $pros))
            {
                return false;
            }
            
            $products = Products::singleton();
            $prosFromDB = self::getGoodsFromDB($pros);
            if (empty($prosFromDB))
            {
                return $rules->delProducts($ruleID, $pros);
            }
            
            foreach ($prosFromDB as $pro)
            {
                $obj = new Product($pro['goods_id'], $pro['goods_name'], $pro['goods_desc'], $pro['goods_img'], 
                                    $salePrice, $limitPerPerson, $ruleID);
            }
            $products->multiPush();
            
        }
    }
	
	public function productsToCart($pros = array())
	{
		if (empty($pros))
		{
			return false;
		}
		
		$validPros = $this->filter($pros);
		
		if (!empty($validPros))
		{
			return $this->cart->push($validPros);
		}
		else
		{
			return false;
		}
	}
	
	public function cartToQueue($userID)
	{
		$cartData = $this->cart->getAll();
		if (!empty($cartData))
		{
			$queue = new SQueue('cart');
			return $queue->push($cartData);
		}
		else 
		{
			return false;
		}
	}
	
	public function queueToOrder()
	{
	}
	
	protected function filter($pros)
	{
		if (empty($pros))
		{
			throw new \Exception('Empty products, cart filter.');
		}
		else
		{
			foreach ($pros as $k => $v)
			{
				if (empty($v) || !isset($v['goodsID']))
				{
					unset($pros[$k]);
				}
				else
				{
					$goods = $this->products->get($v['goodsID']);
					if ($goods == false || !$this->checkRule($goods, $num))
					{
						unset($pros[$k]);
					}
				}
			}
		}
		
		return $pros;
	}
	
	protected function checkRule($goods, $num)
	{
		if (!is_int($num))
		{
			throw new \Exception('Invalid num, must be number.');
		}
		
		if (!isset($goods['id']))
		{
			return false;
		}
		else if (!isset($goods['limitPerUser']))
		{
			return true;
		}
		else
		{
			$checkProc = function ($num) use ($goods) {
				return ($num > (int) $goods['limitPerUser']);
			};
			
			return $checkProc($num);
		}
	}
	
	protected function queue()
	{
		
	}
	
	static public function getGoodsFromDB($ids)
	{
	    global $db, $ecs;
	    
	    $ids = array_map(function ($v) { return intval($v); }, $ids);
	    $sqlID = implode(',', $ids);
	    
	    $sql = 
	   	    "SELECT g.*, b.brand_name " .
	        "FROM " . $ecs->table('goods') . " AS g " .
	        "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
	        "WHERE g.goods_id IN ($sqlID)";
	    
	    return $db->getAll($sql);
    }
    
    static protected function loadECSGoodsLib($admin = false)
    {
        if ($admin)
        {
            require_once __DIR__ . '/../../admin/includes/lib_goods.php';
        }
        else
        {
    	   require_once __DIR__ . '/../../includes/lib_goods.php';
        }
    }
    	
    static public function getAttrsFromDB($goodsID)
    {
    	self::loadECSGoodsLib();
    	
    	$attrs = \get_goods_attr($goodsID);
    	$newv = array();
    	
    	if (empty($attrs))
    	{
    	    return $newv;
    	}
    	
	    foreach ($attrs as $k => $v)
	    {
	        if (!empty($v['goods_attr_list']))
	        {
	            foreach ($v['goods_attr_list'] as $subk => $subv)
	            {
	                if ($subk > 0)
	                {
            	        $newv[] = array(
            	            'attr_id' => $k,
            	            'name' => $v['attr_name'],
            	            'sub_id' => $subk,
            	            'sub_name' => $subv,
            	            'sbu_price' => $v['goods_attr_price'][$subk] ? $v['goods_attr_price'][$subk] : 0,
            	            );
	                }
	            }
	        }
	    }
    	        
        return $newv;
    }

    static public function getGoodsByID($ids = array())
    {
        global $db, $ecs;
        
        self::loadECSGoodsLib();
        
        $ids = array_map(function ($v) {
            return intval($v);
        }, $ids);
        $sqlID = implode(',', $ids);
        
        $sql = "SELECT g.*, b.brand_name " . "FROM " . 
            $ecs->table('goods') . " AS g " . "LEFT JOIN " . 
            $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE g.goods_id IN ($sqlID)";

        return $db->getAll($sql);
    }
    
    static public function goodsList($a, $b)
    {
        self::loadECSGoodsLib(true);
        return \goods_list($a, $b);
    }
}
/*
$test = new Rules();
$test->clear();
$id = $test->push(new Rule('rule1', time() + 3600, time()+ 24 * 3600));
if ($id !== false)
{
    $test->pushProduct($id, array(1, 2, 3));
    $test->pushProduct($id, array(1, 2, 3, 4, 5));
}

$all = $test->getAll();
var_dump($all);
*/
//$tlsp = new TLSP();
