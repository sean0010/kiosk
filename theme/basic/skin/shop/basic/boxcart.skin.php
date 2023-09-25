<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_SHOP_SKIN_URL.'/style.css">', 0);
$cart_action_url = G5_SHOP_URL.'/cartupdate.php';
?>

<!-- 장바구니 간략 보기 시작 { -->
<aside id="sbsk" class="sbsk">
    <h2 class="s_h2">장바구니 <span class="cart-count"><?php echo get_boxcart_datas_count(); ?></span></h2>
    <form name="skin_frmcartlist" id="skin_sod_bsk_list" method="post" action="<?php echo G5_SHOP_URL.'/cartupdate.php'; ?>">
    <ul>
    <?php
    $cart_datas = get_boxcart_datas(true);
    function itemOptions($it_id, $order_id) {
        global $g5;
        $sql = "select ct_id, ct_option, ct_qty, io_price
                from {$g5['g5_shop_cart_table']} 
                where it_id = '$it_id' and od_id = '$order_id' order by io_type asc, ct_id asc ";
        $result = sql_query($sql);

        $str = '';
        for($i = 0; $row = sql_fetch_array($result); $i++) {
            if($i == 0)
                $str .= '<ul>'.PHP_EOL;
            $price_plus = '';
            if($row['io_price'] >= 0)
                $price_plus = '+';
            $str .= '<li>'.get_text($row['ct_option']).' '.$row['ct_qty'].'개 ('.$price_plus.display_price($row['io_price']).')<buttion class="cart_del_option" type="button" data-ct_id="'.$row['ct_id'].'"><i class="fa fa-trash-o" aria-hidden="true"></i><span class="sound_only">삭제</span></button></li>'.PHP_EOL;
        }

        if($i > 0)
            $str .= '</ul>';

        return $str;
    }
    $i = 0;
    foreach($cart_datas as $row)
    {
        if( !$row['it_id'] ) continue;
        $it_options = itemOptions($row['it_id'], $row['od_id']);

        echo '<li>';
        $it_name = get_text($row['it_name']);
		echo '<div class="">';
        echo '<div class="itemName">'.$it_name.'</div>';
        echo '<span class="itemPrice">';
		echo number_format($row['ct_price']).PHP_EOL;
        echo '</span>'.PHP_EOL;
        echo '<span class="option">';
        echo $it_options;
        echo '</span>'.PHP_EOL;
		echo '</div>';
		echo '<button class="cart_del" type="button" data-it_id="'.$row['it_id'].'"><i class="fa fa-trash-o" aria-hidden="true"></i><span class="sound_only">삭제</span></button>'.PHP_EOL;
        echo '</li>';

        echo '<input type="hidden" name="act" value="buy">';
        echo '<input type="hidden" name="ct_chk['.$i.']" value="1">';
        echo '<input type="hidden" name="it_id['.$i.']" value="'.$row['it_id'].'">';
        echo '<input type="hidden" name="it_name['.$i.']" value="'.$it_name.'">';

        $i++;
    }   //end foreach

    if ($i==0)
        echo '<li class="li_empty">장바구니 상품 없음</li>'.PHP_EOL;
    ?>
    </ul>
    <?php if($i){ ?><div class="btn_buy"><button type="submit" class="btn_submit">구매하기</button></div><?php } ?>
    <a href="<?php echo G5_SHOP_URL; ?>/cart.php" class="go_cart">전체보기</a>
    </form>
</aside>
<script>
jQuery(function ($) {
    $("#sbsk").on("click", ".cart_del", function(e) {
        e.preventDefault();

        var it_id = $(this).data("it_id");
        var $wrap = $(this).closest("li");

        $.ajax({
            url: g5_theme_shop_url+"/ajax.action.php",
            type: "POST",
            data: {
                "it_id" : it_id,
                "action" : "cart_delete"
            },
            dataType: "json",
            async: true,
            cache: false,
            success: function(data, textStatus) {
                if(data.error != "") {
                    alert(data.error);
                    return false;
                }

                $wrap.remove();
            }
        });
    });
    $("#sbsk").on("click", ".cart_del_option", function(e) {
        e.preventDefault();

        const ct_id = $(this).data("ct_id");
        const $wrap = $(this).closest("li");

        $.ajax({
            url: g5_theme_shop_url+"/ajax.action.php",
            type: "POST",
            data: {
                "ct_id" : ct_id,
                "action" : "cart_delete_option"
            },
            dataType: "json",
            async: true,
            cache: false,
            success: function(data, textStatus) {
                if(data.error != "") {
                    alert(data.error);
                    return false;
                }

                $wrap.remove();
            }
        });
    });
});
</script>
<!-- } 장바구니 간략 보기 끝 -->

