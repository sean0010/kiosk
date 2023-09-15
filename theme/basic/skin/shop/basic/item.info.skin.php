<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_SHOP_CSS_URL.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>
<script src="<?php echo G5_THEME_JS_URL; ?>/theme.shop.list.js"></script>

<?php if ($default['de_rel_list_use']) { ?>
<!-- 관련상품 시작 { -->
<section id="sit_rel">
    <h2>관련상품</h2>
    <?php
    $sql0 = "SELECT * FROM {$g5['g5_shop_item_table']} WHERE it_id='$it_id'";
    $validOptionResult = sql_query($sql0);
    $inCategories = '';
    $validOptions = array();
    while ($row = sql_fetch_array($validOptionResult)) {
        if (!empty($row['it_1_subj'])) {
            array_push($validOptions, $row['it_1']);
        }
        if (!empty($row['it_2_subj'])) {
            array_push($validOptions, $row['it_2']);
        }
        if (!empty($row['it_3_subj'])) {
            array_push($validOptions, $row['it_3']);
        }
        if (!empty($row['it_4_subj'])) {
            array_push($validOptions, $row['it_4']);
        }
        if (!empty($row['it_5_subj'])) {
            array_push($validOptions, $row['it_5']);
        }
        if (!empty($row['it_6_subj'])) {
            array_push($validOptions, $row['it_6']);
        }
        if (!empty($row['it_7_subj'])) {
            array_push($validOptions, $row['it_7']);
        }
        if (!empty($row['it_8_subj'])) {
            array_push($validOptions, $row['it_8']);
        }
        if (!empty($row['it_9_subj'])) {
            array_push($validOptions, $row['it_9']);
        }
        if (!empty($row['it_10_subj'])) {
            array_push($validOptions, $row['it_10']);
        }
    }
    //$_SESSION['valid_options'] = $validOptions;

    $rel_skin_file = $skin_dir.'/'.$default['de_rel_list_skin'];
    if(!is_file($rel_skin_file))
        $rel_skin_file = G5_SHOP_SKIN_PATH.'/'.$default['de_rel_list_skin'];

    // $ca_id_len = strlen($ca_id);
	// $len2 = $ca_id_len + 2;
	// $sql = " select ca_id from {$g5['g5_shop_category_table']} where ca_id like '$ca_id%' and length(ca_id) = $len2 and ca_use = '1' order by ca_order, ca_id ";
	// $result = sql_query($sql);
    // while ($row=sql_fetch_array($result)) {
    // 	$ca_id2 = $row['ca_id'];
    // 	$sql2 = "SELECT * FROM {$g5['g5_shop_item_table']} WHERE ca_id2 = '$ca_id2'";// AND ca_use = '1'";
    // 	$list = new item_list($rel_skin_file, $default['de_rel_list_mod'], 0, $default['de_rel_img_width'], $default['de_rel_img_height']);
    // 	$list->set_query($sql2);
    // 	echo $list->run();
    // }
    foreach ($validOptions as $key => $ca_id2) {
    	$sql2 = "SELECT * FROM {$g5['g5_shop_item_table']} WHERE ca_id2 = '$ca_id2'";// AND ca_use = '1'";
    	$list = new item_list($rel_skin_file, $default['de_rel_list_mod'], 0, $default['de_rel_img_width'], $default['de_rel_img_height']);
    	$list->set_query($sql2);
    	echo $list->run();
	}
    ?>
</section>
<!-- } 관련상품 끝 -->
<?php } ?>


<a href="#" class="addToCart">카트에담기</a>

<script>
jQuery(function($){
    var change_name = "ct_copy_qty";

    $(document).on("select_it_option_change", "select.it_option", function(e, $othis) {
        var value = $othis.val(),
            change_id = $othis.attr("id").replace("it_option_", "it_side_option_");
        
        if( $("#"+change_id).length ){
            $("#"+change_id).val(value).attr("selected", "selected");
        }
    });

    $(document).on("select_it_option_post", "select.it_option", function(e, $othis, idx, sel_count, data) {
        var value = $othis.val(),
            change_id = $othis.attr("id").replace("it_option_", "it_side_option_");
        
        $("select.it_side_option").eq(idx+1).empty().html(data).attr("disabled", false);

        // select의 옵션이 변경됐을 경우 하위 옵션 disabled
        if( (idx+1) < sel_count) {
            $("select.it_side_option:gt("+(idx+1)+")").val("").attr("disabled", true);
        }
    });

    $(document).on("add_sit_sel_option", "#sit_sel_option", function(e, opt) {
        
        opt = opt.replace('name="ct_qty[', 'name="'+change_name+'[');

        var $opt = $(opt);
        $opt.removeClass("sit_opt_list");
        $("input[type=hidden]", $opt).remove();

        $(".sit_sel_option .sit_opt_added").append($opt);

    });

    $(document).on("price_calculate", "#sit_tot_price", function(e, total) {

        $(".sum_section .sit_tot_price").empty().html("<span>총 금액 </span><strong>"+number_format(String(total))+"</strong> 원");

    });

    $(".sit_side_option").on("change", "select.it_side_option", function(e) {
        var idx = $("select.it_side_option").index($(this)),
            value = $(this).val();

        if( value ){
            if (typeof(option_add) != "undefined"){
                option_add = true;
            }

            $("select.it_option").eq(idx).val(value).attr("selected", "selected").trigger("change");
        }
    });

    $(".sit_side_option").on("change", "select.it_side_supply", function(e) {
        var value = $(this).val();

        if( value ){
            if (typeof(supply_add) != "undefined"){
                supply_add = true;
            }

            $("select.it_supply").val(value).attr("selected", "selected").trigger("change");
        }
    });

    $(".sit_opt_added").on("click", "button", function(e){
        e.preventDefault();

        var $this = $(this),
            mode = $this.text(),
            $sit_sel_el = $("#sit_sel_option"),
            li_parent_index = $this.closest('li').index();
        
        if( ! $sit_sel_el.length ){
            alert("el 에러");
            return false;
        }

        switch(mode) {
            case "증가":
                $sit_sel_el.find("li").eq(li_parent_index).find(".sit_qty_plus").trigger("click");
                break;
            case "감소":
                $sit_sel_el.find("li").eq(li_parent_index).find(".sit_qty_minus").trigger("click");
                break;
            case "삭제":
                $sit_sel_el.find("li").eq(li_parent_index).find(".sit_opt_del").trigger("click");
                break;
        }

    });

    $(document).on("sit_sel_option_success", "#sit_sel_option li button", function(e, $othis, mode, this_qty) {
        var ori_index = $othis.closest('li').index();

        switch(mode) {
            case "증가":
            case "감소":
                $(".sit_opt_added li").eq(ori_index).find("input[name^=ct_copy_qty]").val(this_qty);
                break;
            case "삭제":
                $(".sit_opt_added li").eq(ori_index).remove();
                break;
        }
    });

    $(document).on("change_option_qty", "input[name^=ct_qty]", function(e, $othis, val, force_val) {
        var $this = $(this),
            ori_index = $othis.closest('li').index(),
            this_val = force_val ? force_val : val;

        $(".sit_opt_added").find("li").eq(ori_index).find("input[name^="+change_name+"]").val(this_val);
    });

    $(".sit_opt_added").on("keyup paste", "input[name^="+change_name+"]", function(e) {
         var $this = $(this),
             val= $this.val(),
             this_index = $("input[name^="+change_name+"]").index(this);

         $("input[name^=ct_qty]").eq(this_index).val(val).trigger("keyup");
    });

    $(".sit_order_btn").on("click", "button", function(e){
        e.preventDefault();

        var $this = $(this);

        if( $this.hasClass("sit_btn_cart") ){
            $("#sit_ov_btn .sit_btn_cart").trigger("click");
        } else if ( $this.hasClass("sit_btn_buy") ) {
            $("#sit_ov_btn .sit_btn_buy").trigger("click");
        }
    });
});
</script>