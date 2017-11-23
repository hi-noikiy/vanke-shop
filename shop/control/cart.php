<?php
/**
 * 购物车操作
 ***/



class cartControl extends BaseBuyControl {

	public function __construct() {
		parent::__construct();
		Language::read('home_cart_index');
		Tpl::output('hidden_nctoolbar', 1);

		$op = isset($_GET['op']) ? $_GET['op'] : $_POST['op'];

		//允许不登录就可以访问的op
		$op_arr = array('ajax_load','add','del');
		if (!in_array($op,$op_arr) && !$_SESSION['member_id'] ){
			$current_url = request_uri();
			redirect('index.php?act=login&ref_url='.urlencode($current_url));
		}
		Tpl::output('hidden_rtoolbar_cart', 1);
	}

	/**
	 * 购物车首页
     * ==============
	 */
	public function indexOp() {
//        header("Location: index.php?act=cart&op=check_card_num"); 
        $model_cart	= Model('cart');
        $logic_buy_1 = logic('buy_1');

        //购物车列表
        $cart_list	= $model_cart->listCart('db',array('buyer_id'=>$_SESSION['member_id']));

        //购物车列表 [得到最新商品属性及促销信息] 
        $cart_list = $logic_buy_1->getGoodsCartList($cart_list);
        
        //购物车商品以店铺ID分组显示,并计算商品小计,店铺小计与总价由JS计算得出
        $store_cart_list = array();
        foreach ($cart_list as $cart) {
            $cart['goods_total'] = ncPriceFormat($cart['goods_price'] * $cart['goods_num']);
            $store_cart_list[$cart['store_id']][] = $cart;
        }
        Tpl::output('store_cart_list',$store_cart_list);

        //店铺信息
        $store_list = Model('store')->getStoreMemberIDList(array_keys($store_cart_list));
        Tpl::output('store_list',$store_list);

        //取得店铺级活动 - 可用的满即送活动
	    $mansong_rule_list = $logic_buy_1->getMansongRuleList(array_keys($store_cart_list));
	    Tpl::output('mansong_rule_list',$mansong_rule_list);

	    //取得哪些店铺有满免运费活动
        $free_freight_list = $logic_buy_1->getFreeFreightActiveLists(array_keys($store_cart_list));
        Tpl::output('free_freight_list',$free_freight_list);

        //标识 购买流程执行第几步
	    Tpl::output('buy_step','step1');
        Tpl::showpage(empty($cart_list) ? 'cart_empty' : 'cart');
	}

	/**
	 * 异步查询购物车
	 */
	public function ajax_loadOp() {
	    $model_cart	= Model('cart');
		if ($_SESSION['member_id']){
		    //登录后
			$cart_list	= $model_cart->listCart('db',array('buyer_id'=>$_SESSION['member_id']));
			$cart_array	= array();
			if(!empty($cart_list)){
				foreach ($cart_list as $k => $cart){
					$cart_array['list'][$k]['cart_id'] = $cart['cart_id'];
					$cart_array['list'][$k]['goods_id'] = $cart['goods_id'];
					$cart_array['list'][$k]['goods_name'] = $cart['goods_name'];
					$cart_array['list'][$k]['goods_price'] 	= $cart['goods_price'];
					$cart_array['list'][$k]['goods_image']	= thumb($cart,60);
					$cart_array['list'][$k]['goods_num'] = $cart['goods_num'];
                    $cart_array['list'][$k]['goods_min'] = $cart['goods_min'];
                    $cart_array['list'][$k]['goods_max'] = $cart['goods_max'];
					$cart_array['list'][$k]['goods_url'] = urlShop('goods', 'index', array('goods_id' => $cart['goods_id']));
				}
			}
		} else {
		    //登录前
			$cart_list = $model_cart->listCart('cookie');
			foreach ($cart_list as $key => $cart){
			    $value = array();
			    $value['cart_id'] = $cart['goods_id'];
				$value['goods_name'] = $cart['goods_name'];
				$value['goods_price'] = $cart['goods_price'];
				$value['goods_num'] = $cart['goods_num'];
                $value['goods_min'] = $cart['goods_min'];
                $value['goods_max'] = $cart['goods_max'];
				$value['goods_image'] = thumb($cart,60);
				$value['goods_url'] = urlShop('goods', 'index', array('goods_id' => $cart['goods_id']));
				$cart_array['list'][] = $value;
			}
		}
		setNcCookie('cart_goods_num',$model_cart->cart_goods_num,2*3600);
		$cart_array['cart_all_price'] = ncPriceFormat($model_cart->cart_all_price);
		$cart_array['cart_goods_num'] = $model_cart->cart_goods_num;
		if ($_GET['type'] == 'html') {
		    Tpl::output('cart_list',$cart_array);
		    Tpl::showpage('cart_mini','null_layout');
		} else {
		    $cart_array = strtoupper(CHARSET) == 'GBK' ? Language::getUTF8($cart_array) : $cart_array;
		    $json_data = json_encode($cart_array);
		    if (isset($_GET['callback'])) {
		        $json_data = $_GET['callback']=='?' ? '('.$json_data.')' : $_GET['callback']."($json_data);";
		    }
		    exit($json_data);		    
		}

	}

	/**
	 * 加入购物车，登录后存入购物车表
	 * 存入COOKIE，由于COOKIE长度限制，最多保存5个商品
	 * 未登录不能将优惠套装商品加入购物车，登录前保存的信息以goods_id为下标
	 *
	 */
	public function addOp() {
	    $model_goods = Model('goods');
	    $logic_buy_1 = Logic('buy_1');
        if (is_numeric($_GET['goods_id'])) {

            //商品加入购物车(默认)
            $goods_id = intval($_GET['goods_id']);
            $quantity = intval($_GET['quantity']);
            if ($goods_id <= 0) return ;
            $goods_info	= $model_goods->getGoodsOnlineInfoAndPromotionById($goods_id);
            if($_SESSION['identity'] == MEMBER_IDENTITY_TWO){//这是采购员
                $goods_info['goods_price'] = $goods_info['goods_price'];
            }else if($_SESSION['identity'] == MEMBER_IDENTITY_FIVE){//这是第三方采购员
                $goods_info['goods_price'] = $goods_info['goods_third_price'];
            }else{
                $goods_info['goods_price'] = $goods_info['goods_toc_price'];
            }
            //抢购
            $logic_buy_1->getGroupbuyInfo($goods_info);

            //限时折扣
            $logic_buy_1->getXianshiInfo($goods_info,$quantity);

            $this->_check_goods($goods_info,$_GET['quantity']);

        } elseif (is_numeric($_GET['bl_id'])) {

            //优惠套装加入购物车(单套)
            if (!$_SESSION['member_id']) {
                exit(json_encode(array('msg'=>'请先登录','UTF-8')));
            }
            $bl_id = intval($_GET['bl_id']);
            if ($bl_id <= 0) return ;
            $model_bl = Model('p_bundling');
            $bl_info = $model_bl->getBundlingInfo(array('bl_id'=>$bl_id));
            if (empty($bl_info) || $bl_info['bl_state'] == '0') {
                exit(json_encode(array('msg'=>'该优惠套装已不存在，建议您单独购买','UTF-8')));
            }

            //检查每个商品是否符合条件,并重新计算套装总价
            $bl_goods_list = $model_bl->getBundlingGoodsList(array('bl_id'=>$bl_id));
            $goods_id_array = array();
            $bl_amount = 0;
            foreach ($bl_goods_list as $goods) {
            	$goods_id_array[] = $goods['goods_id'];
            	$bl_amount += $goods['bl_goods_price'];
            }
            $model_goods = Model('goods');
            $goods_list = $model_goods->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);
            foreach ($goods_list as $goods) {
                $this->_check_goods($goods,1);
            }

            //优惠套装作为一条记录插入购物车，图片取套装内的第一个商品图
            $goods_info    = array();
            $goods_info['store_id']	= $bl_info['store_id'];
            $goods_info['goods_id']	= $goods_list[0]['goods_id'];
            $goods_info['goods_name'] = $bl_info['bl_name'];
            $goods_info['goods_price'] = $bl_amount;
            $goods_info['goods_num']   = 1;
            $goods_info['goods_image'] = $goods_list[0]['goods_image'];
            $goods_info['store_name'] = $bl_info['store_name'];
            $goods_info['bl_id'] = $bl_id;
            $quantity = 1;
        }

        //已登录状态，存入数据库,未登录时，存入COOKIE
        if($_SESSION['member_id']) {
            $save_type = 'db';
            $goods_info['buyer_id'] = $_SESSION['member_id'];
        } else {
            $save_type = 'cookie';
        }
        $model_cart	= Model('cart');
        $car_list_where['store_id']	= $goods_info['store_id'];
        $car_list_where['goods_id']	= $goods_info['goods_id'];
        $car_list_where['buyer_id']     = $_SESSION['member_id'];
        $car_list = $model_cart->where($car_list_where)->field('goods_num')->find();
        if($car_list != false){
            $updata_insert_data['goods_num'] = $car_list['goods_num'] + $quantity;
            $insert = $model_cart->where($car_list_where)->update($updata_insert_data);
            $model_cart->getCartNum($save_type,array('buyer_id'=>$_SESSION['member_id']));
        }else{
            $insert = $model_cart->addCart($goods_info,$save_type,$quantity);
//            $car_list_num = cookie('cart_goods_num')+1;
        }
        if ($insert) {
            //购物车商品种数记入cookie
            setNcCookie('cart_goods_num',$model_cart->cart_goods_num,2*3600);
            $data = array('state'=>'true', 'num' => $model_cart->cart_goods_num, 'amount' => ncPriceFormat($model_cart->cart_all_price));
        } else {
            $data = array('state'=>'false');
        }
	    exit(json_encode($data));
	}

	/**
	 * 加入购物车，登录后存入购物车表
	 * 存入COOKIE，由于COOKIE长度限制，最多保存5个商品
	 * 未登录不能将优惠套装商品加入购物车，登录前保存的信息以goods_id为下标
	 *
	 */
	public function addCartBatchOp() {
		$model_goods = Model('goods');
		$logic_buy_1 = Logic('buy_1');
		$len = sizeof($_GET['goodIdArray']);
		for($i=0;$i<$len;$i++){
			$temGood = $_GET['goodIdArray'][$i];
			if (is_numeric($temGood['goods_id'])) {
				//商品加入购物车(默认)
				$goods_id = intval($temGood['goods_id']);
				$quantity = intval($temGood['quantity']);
				if ($goods_id <= 0) return ;
				$goods_info	= $model_goods->getGoodsOnlineInfoAndPromotionById($goods_id);

				if($_SESSION['identity'] == MEMBER_IDENTITY_TWO){//这是采购员
					$goods_info['goods_price'] = $goods_info['goods_price'];
				}else if($_SESSION['identity'] == MEMBER_IDENTITY_FIVE){//这是第三方采购员
					$goods_info['goods_price'] = $goods_info['goods_third_price'];
				}else{
					$goods_info['goods_price'] = $goods_info['goods_toc_price'];
				}
				//抢购
				$logic_buy_1->getGroupbuyInfo($goods_info);

				//限时折扣
				$logic_buy_1->getXianshiInfo($goods_info,$quantity);

				$this->_check_goods($goods_info,$temGood['quantity']);

			}

			//已登录状态，存入数据库,未登录时，存入COOKIE
			if($_SESSION['member_id']) {
				$save_type = 'db';
				$goods_info['buyer_id'] = $_SESSION['member_id'];
			}
			$model_cart	= Model('cart');

			$car_list_where['store_id']	= $goods_info['store_id'];
			$car_list_where['goods_id']	= $goods_info['goods_id'];
			$car_list_where['buyer_id']     = $_SESSION['member_id'];
			$car_list = $model_cart->where($car_list_where)->field('goods_num')->find();
			if($car_list != false){
				$updata_insert_data['goods_num'] = $car_list['goods_num'] + $quantity;
				$insert = $model_cart->where($car_list_where)->update($updata_insert_data);
				$model_cart->getCartNum($save_type,array('buyer_id'=>$_SESSION['member_id']));
			}else{
				$insert = $model_cart->addCart($goods_info,$save_type,$quantity);
			}
			if ($insert) {
				//购物车商品种数记入cookie
				setNcCookie('cart_goods_num',$model_cart->cart_goods_num,2*3600);
				$data = array('state'=>'true', 'num' => $model_cart->cart_goods_num, 'amount' => ncPriceFormat($model_cart->cart_all_price));
			} else {
				$data = array('state'=>'false');
			}
		}

		exit(json_encode($data));
	}
	/**
	 * 推荐组合加入购物车
	 */
	public function add_combOp() {
	    if (!preg_match('/^[\d|]+$/', $_GET['goods_ids'])) {
	        exit(json_encode(array('state'=>'false')));
	    }

	    $model_goods = Model('goods');
	    $logic_buy_1 = Logic('buy_1');
	
        if (!$_SESSION['member_id']) {
            exit(json_encode(array('msg'=>'请先登录','UTF-8')));
        }

        $goods_id_array = explode('|', $_GET['goods_ids']);

        $model_goods = Model('goods');
        $goods_list = $model_goods->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);
        
        foreach ($goods_list as $goods) {
            $this->_check_goods($goods,1);
        }

        //抢购
        $logic_buy_1->getGroupbuyCartList($goods_list);

        //限时折扣
        $logic_buy_1->getXianshiCartList($goods_list);

        $model_cart	= Model('cart');
        foreach ($goods_list as $goods_info) {
            $cart_info = array();
            $cart_info['store_id']	= $goods_info['store_id'];
            $cart_info['goods_id']	= $goods_info['goods_id'];
            $cart_info['goods_name'] = $goods_info['goods_name'];
            $cart_info['goods_price'] = $goods_info['goods_price'];
            $cart_info['goods_num']   = 1;
            $cart_info['goods_image'] = $goods_info['goods_image'];
            $cart_info['store_name'] = $goods_info['store_name'];
            $cart_info['good_min'] = $goods_info['min_num'];
            $cart_info['good_max'] = $goods_info['max_num'];
            $quantity = 1;
    	    //已登录状态，存入数据库,未登录时，存入COOKIE
    	    if($_SESSION['member_id']) {
    	        $save_type = 'db';
    	        $cart_info['buyer_id'] = $_SESSION['member_id'];
    	    } else {
    	        $save_type = 'cookie';
    	    }
    	    $insert = $model_cart->addCart($cart_info,$save_type,$quantity);
    	    if ($insert) {
    	        //购物车商品种数记入cookie
    	        setNcCookie('cart_goods_num',$model_cart->cart_goods_num,2*3600);
    	        $data = array('state'=>'true', 'num' => $model_cart->cart_goods_num, 'amount' => ncPriceFormat($model_cart->cart_all_price));
    	    } else {
    	        $data = array('state'=>'false');
    	        exit(json_encode($data));
    	    }
        }
        exit(json_encode($data));
	}

	/**
	 * 检查商品是否符合加入购物车条件
	 * @param unknown $goods
	 * @param number $quantity
	 */
	private function _check_goods($goods_info, $quantity) {
		if(empty($quantity)) {
			exit(json_encode(array('msg'=>Language::get('wrong_argument','UTF-8'))));
		}
		if(empty($goods_info)) {
			exit(json_encode(array('msg'=>Language::get('cart_add_goods_not_exists','UTF-8'))));
		}
		if ($goods_info['store_id'] == $_SESSION['store_id']) {
			exit(json_encode(array('msg'=>Language::get('cart_add_cannot_buy','UTF-8'))));
		}
		if(intval($goods_info['goods_storage']) < 1) {
			exit(json_encode(array('msg'=>Language::get('cart_add_stock_shortage','UTF-8'))));
		}
		//库存
		if(intval($goods_info['goods_storage']) < $quantity) {
			exit(json_encode(array('msg'=>Language::get('cart_add_too_much','UTF-8'))));
		}
		if ($goods_info['is_virtual'] || $goods_info['is_fcode'] || $goods_info['is_presell']) {
		    exit(json_encode(array('msg'=>'该商品不允许加入购物车，请直接购买','UTF-8')));
		}
		//最小购买
		if(intval($goods_info['min_num']) > $quantity){
            exit(json_encode(array('msg'=>'该商品允许最小购买数量为'.$goods_info['min_num'],'UTF-8')));
        }
        //最大购买
        if(intval($goods_info['max_num']) < $quantity){
            exit(json_encode(array('msg'=>'该商品允许最大购买数量为'.$goods_info['max_num'],'UTF-8')));
        }
	}

	/**
	 * 购物车更新商品数量
	 */
	public function updateOp() {
		$cart_id	= intval(abs($_GET['cart_id']));
		$quantity	= intval(abs($_GET['quantity']));

		if(empty($cart_id) || empty($quantity)) {
			exit(json_encode(array('msg'=>Language::get('cart_update_buy_fail','UTF-8'))));
		}

		$model_cart = Model('cart');
		$model_goods= Model('goods');
		$logic_buy_1 = logic('buy_1');

		//存放返回信息
		$return = array();

		$cart_info = $model_cart->getCartInfo(array('cart_id'=>$cart_id,'buyer_id'=>$_SESSION['member_id']));
		if ($cart_info['bl_id'] == '0') {

		    //普通商品
		    $goods_id = intval($cart_info['goods_id']);
		    $goods_info	= $logic_buy_1->getGoodsOnlineInfo($goods_id,$quantity);
                    
		    if(empty($goods_info)) {
		        $return['state'] = 'invalid';
		        $return['msg'] = '商品已被下架';
		        $return['subtotal'] = 0;
		        QueueClient::push('delCart', array('buyer_id'=>$_SESSION['member_id'],'cart_ids'=>array($cart_id)));
		        exit(json_encode($return));
		    }

		    //抢购
		    $logic_buy_1->getGroupbuyInfo($goods_info);

		    //限时折扣
		    $logic_buy_1->getXianshiInfo($goods_info,$quantity);

		    $quantity = $goods_info['goods_num'];

		    if(intval($goods_info['goods_storage']) < $quantity) {
		        $return['state'] = 'shortage';
		        $return['msg'] = '库存不足';
		        $return['goods_num'] = $goods_info['goods_num'];
		        $return['goods_price'] = $goods_info['goods_price'];
		        $return['subtotal'] = $goods_info['goods_price'] * $quantity;
		        $model_cart->editCart(array('goods_num'=>$goods_info['goods_storage']),array('cart_id'=>$cart_id,'buyer_id'=>$_SESSION['member_id']));
		        exit(json_encode($return));
		    }
		} else {

		    //优惠套装商品
		    $model_bl = Model('p_bundling');
		    $bl_goods_list = $model_bl->getBundlingGoodsList(array('bl_id'=>$cart_info['bl_id']));
		    $goods_id_array = array();
		    foreach ($bl_goods_list as $goods) {
		        $goods_id_array[] = $goods['goods_id'];
		    }
		    $goods_list = $model_goods->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);

		    //如果其中有商品下架，删除
		    if (count($goods_list) != count($goods_id_array)) {
		        $return['state'] = 'invalid';
		        $return['msg'] = '该优惠套装已经无效，建议您购买单个商品';
		        $return['subtotal'] = 0;
		        QueueClient::push('delCart', array('buyer_id'=>$_SESSION['member_id'],'cart_ids'=>array($cart_id)));
		        exit(json_encode($return));
		    }

		    //如果有商品库存不足，更新购买数量到目前最大库存
		    foreach ($goods_list as $goods_info) {
		        if ($quantity > $goods_info['goods_storage']) {
		            $return['state'] = 'shortage';
		            $return['msg'] = '该优惠套装部分商品库存不足，建议您降低购买数量或购买库存足够的单个商品';
		            $return['goods_num'] = $goods_info['goods_storage'];
		            $return['goods_price'] = $cart_info['goods_price'];
		            $return['subtotal'] = $cart_info['goods_price'] * $quantity;
		            $model_cart->editCart(array('goods_num'=>$goods_info['goods_storage']),array('cart_id'=>$cart_id,'buyer_id'=>$_SESSION['member_id']));
		            exit(json_encode($return));
		            break;
		        }
		    }
		    $goods_info['goods_price'] = $cart_info['goods_price'];
		}

		$data = array();
        $data['goods_num'] = $quantity;
        $data['goods_price'] = $goods_info['goods_price'];
        $update = $model_cart->editCart($data,array('cart_id'=>$cart_id,'buyer_id'=>$_SESSION['member_id']));
		if ($update) {
		    $return = array();
			$return['state'] = 'true';
			$return['subtotal'] = $goods_info['goods_price'] * $quantity;
			$return['goods_price'] = $goods_info['goods_price'];
			$return['goods_num'] = $quantity;
		} else {
			$return = array('msg'=>Language::get('cart_update_buy_fail','UTF-8'));
		}
		exit(json_encode($return));
	}

	/**
	 * 购物车删除单个商品，未登录前使用cart_id即为goods_id
	 */
	public function delOp() {
		$cart_id = intval($_GET['cart_id']);
		if($cart_id < 0) return ;
		$model_cart	= Model('cart');
		$data = array();
		if ($_SESSION['member_id']) {
		    //登录状态下删除数据库内容
			$delete	= $model_cart->delCart('db',array('cart_id'=>$cart_id,'buyer_id'=>$_SESSION['member_id']));
			if($delete) {
			    $data['state'] = 'true';
			    $data['quantity'] = $model_cart->cart_goods_num;
			    $data['amount'] = $model_cart->cart_all_price;
			} else {
				$data['msg'] = Language::get('cart_drop_del_fail','UTF-8');
			}
		} else {
			//未登录时删除cookie的购物车信息
			$delete	= $model_cart->delCart('cookie',array('goods_id'=>$cart_id));
			if($delete) {
			    $data['state'] = 'true';
			    $data['quantity'] = $model_cart->cart_goods_num;
			    $data['amount'] = $model_cart->cart_all_price;
			}
		}
		setNcCookie('cart_goods_num',$model_cart->cart_goods_num,2*3600);
		$json_data = json_encode($data);
        if (isset($_GET['callback'])) {
            $json_data = $_GET['callback']=='?' ? '('.$json_data.')' : $_GET['callback']."($json_data);";
        }
        exit($json_data);
	}
        
        public function check_goods_cityOp(){
            //验证判断当前会员是否是采购员
            if(empty($_POST['goods_id'])){
                echo "2";exit;
            }else{
                $goods_id = htmlspecialchars($_POST['goods_id']);
            }
            $model = Model();
            $member_cityid = explode(',', $_SESSION['city_id']);
            
            //供应商不能购买商品
            if($_SESSION['identity'] == MEMBER_IDENTITY_THREE || $_SESSION['identity'] == MEMBER_IDENTITY_FOUR){
                echo "3";exit;
            }
            
            if(is_array($member_cityid) && $_SESSION['city_id']){
                //如果是有城市中心id的，则是采购员 判断当前店铺是否有当前采购员的城市id
                //
                //获取商品信息 查找当前商家的城市id 是否 含有当前采购员的采购城市ID 如果有则继续
                //如果不满足则返回提示采购员不能购买当前区域的商品
                $store_id = $model->table('goods,store')->join('left')->on('goods.store_id=store.store_id')->field('store.store_city_id')->where('goods.goods_id='.$goods_id)->find();

                $city_array = explode(',', $store_id['store_city_id']);
                foreach($city_array as $store_city){
                    foreach($member_cityid as $member_city){
                        if($store_city == $member_city){
                            $is_in_city = 1;
                        }
                    }
                }
                if($is_in_city == 1){
                    echo "1";exit;
                }else{
                    echo "3";exit;
                }
                //判断当前提交过来的商品信息是否在当前采购员可购买的城市中心中
            }else if($_SESSION['identity'] == MEMBER_IDENTITY_FIVE){
                
                $store_id = $model->table('goods,store')->join('left')->on('goods.store_id=store.store_id')->field('store.store_city_id')->where('goods.goods_id='.$goods_id)->find();

                $city_array = explode(',', $store_id['store_city_id']);
                foreach($city_array as $store_city){
                        if($store_city == 1){
                            $is_in_city = 1;
                        }
                }
                
                if($is_in_city == 1){
                    echo "1";exit;
                }else{
                    echo "3";exit;
                }
                
            }else{
                //如果不是采购员则直接进入正轨添加购物车流程返回1
                echo "1";exit;
            }
            
        }
        
        public function check_card_numOp(){
            
            $model_cart	= Model('cart');
            $logic_buy_1 = logic('buy_1');

            if($_POST['ifcart'] == 1){
                foreach ($_POST['cart_id'] as $c_car_id){
                    $cart_rid[] = explode('|',$c_car_id);
                }
            }
            $cid = '';
            foreach($cart_rid as $db_carid){
                $cid .= $db_carid[0].',';
            }
            //购物车列表
            $cart_list	= $model_cart->listCart('db',array('buyer_id'=>$_SESSION['member_id'],'cart_id'=>array('in',$cid)));

            //购物车列表 [得到最新商品属性及促销信息] 
            $cart_list = $logic_buy_1->getGoodsCartList($cart_list);

            $cart_list = $this->check_num_apiOp($cart_list);
            //购物车商品以店铺ID分组显示,并计算商品小计,店铺小计与总价由JS计算得出
            $store_cart_list = array();
            foreach ($cart_list as $cart) {
                $cart['goods_total'] = ncPriceFormat($cart['goods_price'] * $cart['goods_num']);
                $store_cart_list[$cart['store_id']][] = $cart;
            }
            
            Tpl::output('store_cart_list',$store_cart_list);

            //店铺信息
            $store_list = Model('store')->getStoreMemberIDList(array_keys($store_cart_list));
            Tpl::output('store_list',$store_list);

            //取得店铺级活动 - 可用的满即送活动
                $mansong_rule_list = $logic_buy_1->getMansongRuleList(array_keys($store_cart_list));
                Tpl::output('mansong_rule_list',$mansong_rule_list);

                //取得哪些店铺有满免运费活动
            $free_freight_list = $logic_buy_1->getFreeFreightActiveList(array_keys($store_cart_list));
            Tpl::output('free_freight_list',$free_freight_list);

            
            //标识 购买流程执行第几步
            Tpl::output('buy_step','step1');
            Tpl::showpage(empty($cart_list) ? 'cart_empty' : 'cart_check');
        }
        
        public function check_num_apiOp($data){
            //检查商品库存是否够
            foreach($data as $rows){
                $goods_id .= $rows['goods_id'].",";
            }
            //查询当前商品的物料ID
            $model= Model();
            $goods_where['goods_id'] = array('in',$goods_id);
            $goods = $model->table('goods')->field('goods_id,materiel_code')->where($goods_where)->select();
            //整理数据提交到API
            foreach($goods as $send_api){
                $data_send[] = $send_api['materiel_code'];
            }

            //查询pernr_id
           $pid = $model->table('member')->field('pernr_id')->where('member_id='.$_SESSION['member_id'])->find();
           $send_data['pernr_id'] = $pid['pernr_id'];
           $send_data['materiel_code'] = $data_send;
           $re_data = $this->get_apipostOp($send_data);


            //把物料编号写入商品数据
            if($re_data['resultCode'] == '0'){
                foreach($re_data['resultData'] as $a){
                    foreach($goods as $key=>$b){
                        if($a['materiel_code'] == $b['materiel_code']){
                            $goods[$key]['materiel_min'] = $a['materiel_min'];
                            $goods[$key]['materiel_now'] = $a['materiel_now'];                      
                        }
                    }   
                }
            }
            //把商品信息返回给前台
            foreach($data as $key=>$a){
                foreach($goods as $b){
                    if($a['goods_id'] == $b['goods_id']){
			if(is_null($b['materiel_min']))$b['materiel_min']='库存异常';
			if(is_null($b['materiel_now']))$b['materiel_now']='库存异常';
                        $data[$key]['materiel_min'] = $b['materiel_min'].$re_data['resultData'][$key]['unit'];
                        $data[$key]['materiel_now'] = $b['materiel_now'].$re_data['resultData'][$key]['unit'];
                    }
                }
            }
            return $data; 
        }
        //商品详情页请求
        public function get_goodsnum_apiOp(){
            $model = Model();
            $goods_id = htmlspecialchars($_POST['goods_id']);
            $goods_materiel_code = $model->table('goods')->where('goods_id='.$goods_id)->field('materiel_code')->find();
            $data = array();
            $data[] = $goods_materiel_code['materiel_code'];
            
             //查询pernr_id
           $pid = $model->table('member')->field('pernr_id')->where('member_id='.$_SESSION['member_id'])->find();
           $send_data['pernr_id'] = $pid['pernr_id'];
           $send_data['materiel_code'] = $data;
            
            $re = $this->get_apipostOp($send_data);
            if($re['resultCode'] == '0'){
                foreach($re['resultData'] as $rows){
                    if($rows['materiel_min'] == 'null'){
                        $rows['materiel_min']='无库存';                            
                    }
                    if($rows['materiel_now'] == 'null'){
                        $rows['materiel_now']='无库存';
                    }
                    if(intval($rows['materiel_now']) > $rows['materiel_min']){
                        $msg = "当前商品最低库存为：".$rows['materiel_min'].$rows['unit'].",当前库存是：".$rows['materiel_now'].$rows['unit'];
                        $msg .= "\n\r当前库存已大于最低库存";
                    }
                }
            }else{
                //$msg = "前商品最低库存为：0,当前库存是：0";
		$msg='库存异常';
            }
            echo $msg;
        }
        /**
        * 向雅马哈采购系统请求物料编码对应商品的最低库存值以及现在库存值
        * @param type $data_send 数组 key1[pernr_id] 采购员id  key2[materiel_code] 物料编码一维数组 格式：[0=> 1001 ,  1=> 1002]
        * @return string
        */      
        public  function get_apipostOp($data_send){
            try {
                    if(!empty($data_send['materiel_code'])){
			$json_array =array();
			for($i = 0;$i< sizeof($data_send['materiel_code']);$i++){
				$json_array[$i]=array(
					'materiel_code'=>$data_send['materiel_code'][$i]
                                );
			}
                    }else{
                        log::record4inter('商品数量为空', log::INFO);
			return $data = array(
				'resultCode'=>'-1',
				'resultMsg='=>'商品数量为0',
				'resultData'=>''
			);
                    }
            	//制作特定格式的数据以json形式传送给采购平台
                $dataOutPut = array();
                $dataOutPut['purchasePicCd'] = $data_send['pernr_id'];
                $dataOutPut['jsonArray']=$json_array;
                $dataOutPut = json_encode($dataOutPut);
            	$url = YMA_WEBSERVICE_RETRIEVE_PRODUCT_STOCK;
            	//向远程服务器请求数据
		$data = WebServiceUtil::getDataByCurl($url, $dataOutPut, 0);
		$data = json_decode($data,true);
            } catch (Exception $exc) {
                log::record4inter($dataOutPut.' query error:'.$exc->getMessage(), log::ERR);
				return $data = array(
                        'resultCode'=>'-1',
                        'resultMsg='=>'请求发送失败',
                        'resultData'=>''
				);
                
            }
            return $data;
        }
}
