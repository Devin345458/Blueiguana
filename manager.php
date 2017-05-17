<?
require("global_header.php");
$manager="active";
require("middle_header.php");
if($_SESSION['ulevel'] >= 2) {
    die("You don't have permission to view this page!");
}

if ( isset($_POST["csvuploadflag"]) && isset($_FILES["file"])) {
  $csv= file_get_contents($_FILES['file']['tmp_name']);
  $array = array_map("str_getcsv", explode("\n", $csv));

  $results = [];
  foreach($array as $i => $item) {
    if($item[0] == "Textbox67") {
      $results['total_sales'] = $array[$i+1][1];
      $results['total_sales'] = str_replace('$', '', $results['total_sales']);
      $results['total_sales'] = str_replace(',', '', $results['total_sales']);
    }
    if(count($item) > 5 && $item[4] == "Textbox181") {
      $results['total_credit_cards'] = $array[$i+1][7];
      $results['total_credit_cards'] = str_replace('$', '', $results['total_credit_cards']);
      $results['total_credit_cards'] = str_replace(',', '', $results['total_credit_cards']);
    }
    if($item[0] == "Fleets") {
      $results['fleet'] = $array[$i][3];
      $results['fleet'] = str_replace('$', '', $results['fleet']);
      $results['fleet'] = str_replace(',', '', $results['fleet']);
    }

    if($item[0] == "Gift Card Redeemed") {
      $results['gift_card_redeemed'] = $array[$i][3];
      $results['gift_card_redeemed'] = str_replace('$', '', $results['gift_card_redeemed']);
      $results['gift_card_redeemed'] = str_replace(',', '', $results['gift_card_redeemed']);
    }

    if($item[0] == "Textbox357") {
      $results['gift_card_sales'] = $array[$i+1][4];
      $results['gift_card_sales'] = str_replace('$', '', $results['gift_card_sales']);
      $results['gift_card_sales'] = str_replace(',', '', $results['gift_card_sales']);
    }
    if($item[0] == "Payout") {
      $results['payouts'] = $array[$i][3];
      $results['payouts'] = str_replace('$', '', $results['payouts']);
      $results['payouts'] = str_replace(',', '', $results['payouts']);
    }

    if($item[0] == "Textbox117") {
      $results['refunds_due'] = $array[$i+1][11];
      $results['refunds_due'] = str_replace('$', '', $results['refunds_due']);
      $results['refunds_due'] = str_replace(',', '', $results['refunds_due']);

      $results['gift_card_free_checks'] = $array[$i+1][15];
      $results['gift_card_free_checks'] = str_replace('$', '', $results['gift_card_free_checks']);
      $results['gift_card_free_checks'] = str_replace(',', '', $results['gift_card_free_checks']);
    }
  }
}


?>
      <link rel="stylesheet" type="text/css" href="css/manager.css">
      <script src='js/bootstrap-filestyle.js'></script>
      <script>

var uploaded_csv_parsed = 0;
function format_money_coins(num3) {
  var numb = parseFloat(Math.round(num3 * 100) / 100).toFixed(2);
  numb = numb.toString();
  return "$"+numb;
}

function format_money(num3) {
  var numb = parseFloat(Math.round(num3 * 100) / 100).toFixed(0);
  numb = numb.toString();
  return "$"+numb;
}

function get_val(el_id) {
  var v = $(el_id).val();
  if (v) {
    return parseFloat(v);
  }
  else {
    return 0;
  }
}

function get_htmlval(el_id) {
  var v = $(el_id).html();
  return Number(v.replace(/[^0-9\.]+/g,""));
}

function get_attrval(el_id) {
  var v = $(el_id).attr('data-amount');
  return Number(v.replace(/[^0-9\.]+/g,""));
}

function gather_data() {
  var input_data = {};
    jQuery('input').each(function(){
      if (jQuery(this).attr('id')) {
        input_data[jQuery(this).attr('id')] = jQuery(this).val();
      }
    });
    input_data['comments_notes'] = jQuery('#manager_comments_notes').val();
    return input_data;
}

function intify(n) {
  if (!n) {
    return 0;
  }
  else {
    return parseInt(n);
  }
}

function floatify(n) {
  if (!n) {
    return 0;
  }
  else {
    return parseFloat(n);
  }
}

var evening_data;
var morning_data;
function get_remote_data() {
  var the_date = new Date($('#mp_date').val());

  var post_data = {};
  post_data['year'] = the_date.getFullYear();
  post_data['month'] = the_date.getMonth()+1;
  post_data['day'] = the_date.getDate();
  $.ajax({
      type: 'POST',
      url: 'api.php?action=get_manager_pull',
      data: post_data,
      dataType: 'json',
      success: function (full_data) {
        jQuery("#manager_table").find('input').val('');
        console.log("get_manager", full_data);
        manager_data = JSON.parse(full_data['manager_pull']);
        for (var key in manager_data) {
          jQuery("#"+key).val(manager_data[key]);
        }
        jQuery("#manager_comments_notes").val(manager_data['comments_notes']);

        evening_data = JSON.parse(full_data['evening_pull']);
        jQuery("#evening_comments_notes").val(evening_data["comments_notes"]);

        jQuery("#evening_mp_name").html(evening_data["mp_name"]);
        jQuery('#evening_mp_name').removeClass('red_bg');
        if (!evening_data["mp_name"]) {
            jQuery('#evening_mp_name').addClass('red_bg');
            jQuery("#evening_mp_name").html("<i>Name</i>");
        }

        var evening_as1_accepter = intify(evening_data['as1_pull1_1']) + intify(evening_data['as1_pull1_5'])*5 + intify(evening_data['as1_pull1_10'])*10 + intify(evening_data['as1_pull1_20'])*20;
        jQuery('#evening_as1_accepter').removeClass('yellow_bg');
        if (!evening_as1_accepter) {
            jQuery('#evening_as1_accepter').addClass('yellow_bg');
        }

        var evening_as2_accepter = intify(evening_data['as2_pull1_1']) + intify(evening_data['as2_pull1_5'])*5 + intify(evening_data['as2_pull1_10'])*10 + intify(evening_data['as2_pull1_20'])*20;
        jQuery('#evening_as2_accepter').removeClass('yellow_bg');
        if (!evening_as2_accepter) {
            jQuery('#evening_as2_accepter').addClass('yellow_bg');
        }

        var evening_as3_accepter = intify(evening_data['as3_pull1_1']) + intify(evening_data['as3_pull1_5'])*5 + intify(evening_data['as3_pull1_10'])*10 + intify(evening_data['as3_pull1_20'])*20;
        jQuery('#evening_as3_accepter').removeClass('yellow_bg');
        if (!evening_as3_accepter) {
            jQuery('#evening_as3_accepter').addClass('yellow_bg');
        }

        var evening_vault_bag_accepter = intify(evening_data['vault_bag_1']) + intify(evening_data['vault_bag_5'])*5 + intify(evening_data['vault_bag_10'])*10 + intify(evening_data['vault_bag_20'])*20 + intify(evening_data['vault_bag_50'])*50 + intify(evening_data['vault_bag_100'])*100;
        jQuery('#evening_vaultbag_accepter').removeClass('red_bg');
        if (evening_vault_bag_accepter != 300) {
            jQuery('#evening_vaultbag_accepter').addClass('red_bg');
        }


        morning_data = JSON.parse(full_data['morning_pull']);
        jQuery("#morning_comments_notes").val(morning_data["comments_notes"]);

        jQuery("#morning_mp_name").html(morning_data["mp_name"]);
        jQuery('#morning_mp_name').removeClass('red_bg');
        if (!morning_data["mp_name"]) {
            jQuery('#morning_mp_name').addClass('red_bg');
            jQuery("#morning_mp_name").html("<i>Name</i>");
        }

        var morning_as1_accepter = intify(morning_data['as1_pull2_1']) + intify(morning_data['as1_pull2_5'])*5 + intify(morning_data['as1_pull2_10'])*10 + intify(morning_data['as1_pull2_20'])*20;
        jQuery('#morning_as1_accepter').removeClass('yellow_bg');
        if (!morning_as1_accepter) {
            jQuery('#morning_as1_accepter').addClass('yellow_bg');
        }

        var morning_as2_accepter = intify(morning_data['as2_pull2_1']) + intify(morning_data['as2_pull2_5'])*5 + intify(morning_data['as2_pull2_10'])*10 + intify(morning_data['as2_pull2_20'])*20;
        jQuery('#morning_as2_accepter').removeClass('yellow_bg');
        if (!morning_as2_accepter) {
            jQuery('#morning_as2_accepter').addClass('yellow_bg');
        }

        var morning_as3_accepter = intify(morning_data['as3_pull2_1']) + intify(morning_data['as3_pull2_5'])*5 + intify(morning_data['as3_pull2_10'])*10 + intify(morning_data['as3_pull2_20'])*20;
        jQuery('#morning_as3_accepter').removeClass('yellow_bg');
        if (!morning_as3_accepter) {
            jQuery('#morning_as3_accepter').addClass('yellow_bg');
        }

        var morning_as1_dispenser = intify(morning_data['as1_1billdispenser']) + intify(morning_data['as1_5billdispenser'])*5;
        jQuery('#morning_as1_dispenser').removeClass('red_bg');
        if (!morning_as1_dispenser || morning_as1_dispenser < 1 || morning_as1_dispenser > 700) {
            jQuery('#morning_as1_dispenser').addClass('red_bg');
        }

        var morning_as2_dispenser = intify(morning_data['as2_1billdispenser']) + intify(morning_data['as2_5billdispenser'])*5;
        jQuery('#morning_as2_dispenser').removeClass('red_bg');
        if (!morning_as2_dispenser || morning_as2_dispenser < 1 || morning_as2_dispenser > 700) {
            jQuery('#morning_as2_dispenser').addClass('red_bg');
        }
        var morning_as3_dispenser = intify(morning_data['as3_1billdispenser']) + intify(morning_data['as3_5billdispenser'])*5;
        jQuery('#morning_as3_dispenser').removeClass('red_bg');
        if (!morning_as3_dispenser || morning_as3_dispenser < 1 || morning_as3_dispenser > 700) {
            jQuery('#morning_as3_dispenser').addClass('red_bg');
        }

        var morning_vault_bag_accepter = intify(morning_data['vault_bag_1']) + intify(morning_data['vault_bag_5'])*5 + intify(morning_data['vault_bag_10'])*10 + intify(morning_data['vault_bag_20'])*20 + intify(morning_data['vault_bag_50'])*50 + intify(morning_data['vault_bag_100'])*100;
        jQuery('#morning_vaultbag_accepter').removeClass('red_bg');
        if (morning_vault_bag_accepter != 300) {
            jQuery('#morning_vaultbag_accepter').addClass('red_bg');
        }

        var morning_pos_accepter = intify(morning_data['pos_1']) + intify(morning_data['pos_5'])*5 + intify(morning_data['pos_10'])*10 + intify(morning_data['pos_20'])*20 + intify(morning_data['pos_50'])*50 + intify(morning_data['pos_100'])*100 + floatify(morning_data['pos_coins']);
        jQuery('#morning_pos').removeClass('yellow_bg');
        if (!morning_pos_accepter || morning_pos_accepter < 50 || morning_pos_accepter > 150) {
            jQuery('#morning_pos').addClass('yellow_bg');
        }

        var morning_vending_accepter = intify(morning_data['vending_1']) + intify(morning_data['vending_5'])*5 + floatify(morning_data['vending_coins']);
        jQuery('#morning_vending').removeClass('yellow_bg');
        if (!morning_vending_accepter) {
            jQuery('#morning_vending').addClass('yellow_bg');
        }

        //Wash Deposit Should Be
        var wash_dep_should = intify(morning_data["as1_1billdispenser"]) + intify(morning_data["as1_5billdispenser"])*5 + intify(morning_data["as2_1billdispenser"]) + intify(morning_data["as2_5billdispenser"])*5 + intify(morning_data["as3_1billdispenser"]) + intify(morning_data["as3_5billdispenser"])*5;
        wash_dep_should += evening_as1_accepter + evening_as2_accepter + evening_as3_accepter;
        wash_dep_should += morning_as1_accepter + morning_as2_accepter + morning_as3_accepter;
        wash_dep_should += morning_vault_bag_accepter + morning_pos_accepter;

        wash_dep_should = wash_dep_should - 2500

        //Wash Deposit Is
        var wash_dep_is = intify(morning_data["wash_deposit_1"]) + intify(morning_data["wash_deposit_5"])*5 + intify(morning_data["wash_deposit_10"])*10 + intify(morning_data["wash_deposit_20"])*20 + intify(morning_data["wash_deposit_50"])*50 + intify(morning_data["wash_deposit_100"])*100 + floatify(morning_data["wash_deposit_coins"])
        $('#wash_deposit_is').html(format_money(wash_dep_is));

        wash_deposit_difference = Math.abs(wash_dep_should-wash_dep_is);

        if (wash_deposit_difference <= 1) {
          $('#morning_wash_deposit').addClass('green_bg');
          $('#morning_wash_deposit').removeClass('red_bg');
          $('#morning_wash_deposit').removeClass('white_bg');
        }
        else if (wash_deposit_difference <= 5) {
          $('#morning_wash_deposit').addClass('white_bg');
          $('#morning_wash_deposit').removeClass('green_bg');
          $('#morning_wash_deposit').removeClass('red_bg');
        }
        else {
          $('#morning_wash_deposit').addClass('red_bg');
          $('#morning_wash_deposit').removeClass('green_bg');
          $('#morning_wash_deposit').removeClass('white_bg');
        }


        var vending_dep_is = intify(morning_data["vending_deposit_1"]) + intify(morning_data["vending_deposit_5"])*5 + intify(morning_data["vending_deposit_10"])*10 + intify(morning_data["vending_deposit_20"])*20 + intify(morning_data["vending_deposit_50"])*50 + intify(morning_data["vending_deposit_100"])*100 + intify(morning_data["vending_deposit_coins"])
        $("#vending_deposit").html(format_money(vending_dep_is));



        var uploaded_csv = <?=json_encode($results)?>;

        var save_me = false;
        console.log("uploaded_csv", uploaded_csv);
        console.log("uploaded_csv_parsed", uploaded_csv_parsed);
        if (!uploaded_csv_parsed) {
          for (var key in uploaded_csv) {
            console.log("CSV UPLOADED, trigger saveme");
            jQuery("#"+key).val(uploaded_csv[key]);
            save_me = true;
          }
          uploaded_csv_parsed = 1;
        }

        update_calculations(save_me);
      },
      error: function(e, type, message) {
        console.log("ERROR", e, type, message);
      }
  });
}

window.onbeforeunload = function(e) {
    if (save_data_dirty_bit || save_data_in_progress || unsaved_data) {
        return "There is still some data saving in the background that may be lost - Please stay on the page for a few more seconds, then you can leave.";
    }
};

var save_data_setimeout;
var save_data_in_progress=0;
var save_data_dirty_bit=0;
var unsaved_data=0;
function save_data() {
  save_data_in_progress = 1;
  save_data_dirty_bit = 0;
  var input_data = gather_data();
  var the_date = new Date($('#mp_date').val());

  var post_data = {};
  post_data['year'] = the_date.getFullYear();
  post_data['month'] = the_date.getMonth()+1;
  post_data['day'] = the_date.getDate();
  post_data['data'] = JSON.stringify(input_data);
  $.ajax({
      type: 'POST',
      url: 'api.php?action=save_manager_pull',
      data: post_data,
      dataType: 'json',
      success: function (data) {
        console.log("sent", data);
          save_data_in_progress = 0;
          unsaved_data = 0;
        if (save_data_dirty_bit) {
            save_data();
        }
      },
      error: function(e, type, message) {
        console.log("ERROR", e, type, message);
          save_data_in_progress = 0;
          unsaved_data = 0;
        if (save_data_dirty_bit) {
            save_data();
        }
      }
  });
}

function update_calculations(save_me) {

  var total_cash = get_val("#total_sales") + get_val("#gift_card_sales") - get_val("#gift_card_free_checks") - get_val("#total_credit_cards") - get_val("#fleet") - get_val("#gift_card_redeemed") + get_val("#refunds_due") - get_val("#payouts");
  $('#total_cash').html(format_money(total_cash));

  var shift_report_difference = total_cash - get_htmlval('#wash_deposit_is');

  var shift_report_difference_text = "";
  var shift_report_difference_abs = Math.abs(shift_report_difference);

  if (shift_report_difference_abs <= 1) {
    $('#shift_report_difference').addClass('green_bg');
    $('#shift_report_difference').removeClass('red_bg');
    $('#shift_report_difference').removeClass('white_bg');
  }
  else if (shift_report_difference_abs <= 5) {
    $('#shift_report_difference').addClass('white_bg');
    $('#shift_report_difference').removeClass('green_bg');
    $('#shift_report_difference').removeClass('red_bg');
  }
  else {
    $('#shift_report_difference').addClass('red_bg');
    $('#shift_report_difference').removeClass('green_bg');
    $('#shift_report_difference').removeClass('white_bg');
  }

  if (shift_report_difference <= -1) {
      shift_report_difference_text = "("+format_money_coins(shift_report_difference_abs)+") Long"
  }
  else if (shift_report_difference >= 1) {
      shift_report_difference_text = format_money_coins(shift_report_difference_abs)+" Short"
  }
  else {
      shift_report_difference_text = "Even"
  }

  $('#shift_report_difference').html(shift_report_difference_text);

  $('#troubleshooting_bill1_emptycol').html(morning_data["vending_deposit_1"]);
  $('#troubleshooting_bill5_emptycol').html(morning_data["vending_deposit_5"]);
  $('#troubleshooting_bill10_emptycol').html(morning_data["vending_deposit_10"]);
  $('#troubleshooting_bill20_emptycol').html(morning_data["vending_deposit_20"]);


  $('#troubleshooting_bill1_endingvaultbag').html(intify(morning_data["vault_bag_1"]) + get_val("#troubleshooting_bill1_putin") - get_val("#troubleshooting_bill1_takenout"));
  $('#troubleshooting_bill5_endingvaultbag').html(intify(morning_data["vault_bag_5"]) + get_val("#troubleshooting_bill5_putin") - get_val("#troubleshooting_bill5_takenout"));
  $('#troubleshooting_bill10_endingvaultbag').html(intify(morning_data["vault_bag_10"]) + get_val("#troubleshooting_bill10_putin") - get_val("#troubleshooting_bill10_takenout"));
  $('#troubleshooting_bill20_endingvaultbag').html(intify(morning_data["vault_bag_20"]) + get_val("#troubleshooting_bill20_putin") - get_val("#troubleshooting_bill20_takenout"));


  $('#troubleshooting_coin_expdep').html(format_money_coins(morning_data["pos_coins"] + morning_data["vending_coins"]));

  var troubleshooting_bill1_expdep = ((intify(morning_data['as1_1billdispenser'])+intify(morning_data['as2_1billdispenser'])+intify(morning_data['as3_1billdispenser'])+intify(morning_data['as1_pull2_1'])+intify(evening_data["as1_pull1_1"])+intify(evening_data["as2_pull1_1"])+intify(morning_data['as2_pull2_1'])+intify(evening_data["as3_pull1_1"])+intify(morning_data['as3_pull2_1'])+intify(morning_data['pos_1']))-650)+intify(morning_data['vending_1'])-get_htmlval('#troubleshooting_bill1_emptycol')-get_val('#troubleshooting_bill1_putin')+get_val('#troubleshooting_bill1_takenout');
  $('#troubleshooting_bill1_expdep').html(troubleshooting_bill1_expdep);

  var troubleshooting_bill5_expdep = ((intify(morning_data['as1_5billdispenser'])+intify(morning_data['as2_5billdispenser'])+intify(morning_data['as3_5billdispenser'])+intify(morning_data['as1_pull2_5'])+intify(evening_data["as1_pull1_5"])+intify(evening_data["as2_pull1_5"])+intify(morning_data['as2_pull2_5'])+intify(evening_data["as3_pull1_5"])+intify(morning_data['as3_pull2_5'])+intify(morning_data['pos_5']))-310)+intify(morning_data['vending_5'])-get_htmlval('#troubleshooting_bill5_emptycol')-get_val('#troubleshooting_bill5_putin')+get_val('#troubleshooting_bill5_takenout');
  $('#troubleshooting_bill5_expdep').html(troubleshooting_bill5_expdep);

  var troubleshooting_bill10_expdep = (intify(morning_data['as1_pull2_10'])+intify(evening_data["as1_pull1_10"])+intify(evening_data["as2_pull1_10"])+intify(morning_data['as2_pull2_10'])+intify(evening_data["as3_pull1_10"])+intify(morning_data['as3_pull2_10'])+intify(morning_data['pos_10']))-get_htmlval('#troubleshooting_bill10_emptycol')-get_val('#troubleshooting_bill10_putin')+get_val('#troubleshooting_bill10_takenout');
  $('#troubleshooting_bill10_expdep').html(troubleshooting_bill10_expdep);

  var troubleshooting_bill20_expdep = (intify(morning_data['as1_pull2_20'])+intify(evening_data["as1_pull1_20"])+intify(evening_data["as2_pull1_20"])+intify(morning_data['as2_pull2_20'])+intify(evening_data["as3_pull1_20"])+intify(morning_data['as3_pull2_20'])+intify(morning_data['pos_20']))-get_htmlval('#troubleshooting_bill20_emptycol')-get_val('#troubleshooting_bill20_putin')+get_val('#troubleshooting_bill20_takenout');
  $('#troubleshooting_bill20_expdep').html(troubleshooting_bill20_expdep);



  $('#troubleshooting_bill1_missingbills').html(troubleshooting_bill1_expdep - intify(morning_data['wash_deposit_1']));
  $('#troubleshooting_bill5_missingbills').html(troubleshooting_bill5_expdep - intify(morning_data['wash_deposit_5']));
  $('#troubleshooting_bill10_missingbills').html(troubleshooting_bill10_expdep - intify(morning_data['wash_deposit_10']));
  $('#troubleshooting_bill20_missingbills').html(troubleshooting_bill20_expdep - intify(morning_data['wash_deposit_20']));



  if (save_me) {
    unsaved_data = 1;
    clearTimeout(save_data_setimeout);

    if (save_data_in_progress) {
      save_data_dirty_bit=1;
    }
    else {
      save_data_setimeout = setTimeout(function(){
        save_data();
      }, 1000);
    }
  }
}

  $( function() {
    $( "#mp_date" ).datepicker();

    $("#manager_table").find("input").keyup(function () {
     update_calculations(true);
    });
    $("#mp_name").keyup(function () {
     update_calculations(true);
    });
    $("#manager_comments_notes").keyup(function () {
     update_calculations(true);
    });


<?
    if($_GET['date']) {
?>
    $( "#mp_date" ).val("<?=$_GET['date']?>");
<?
    }
    else {
?>
    var the_date = new Date();
    var d_string = (the_date.getMonth()+1).toString()+"/"+the_date.getDate().toString()+"/"+the_date.getFullYear().toString();
    $( "#mp_date" ).val(d_string);
<?
    }
?>

    get_remote_data();

  document.getElementById("file").onchange = function() {
  $('#csv_upload').attr('action', '?date='+$('#mp_date').val());
    document.getElementById("csv_upload").submit();
  };

  } );

    function load_page() {
    if ($('#mp_date').val()) {
      window.location.href="?date="+$('#mp_date').val();
    }
  }

      </script>

      <div class="jumbotron" style="background-color: rgb(243, 243, 243) !important; padding-top: 10px !important; padding-right: 15px !important; padding-left: 15px !important;">



      <table style="width: 100%;">
        <tr>
          <td style="text-align: left;vertical-align: top;width:25%;">
            <h3>Blue Iguana Car Wash</h3>
            <h2><?=$_SESSION['location_name']?> <?=$_SESSION['location']?></h2>
          </td>
          <td style="text-align: center;vertical-align: top;width:50%;">
            <h1>&nbsp;</h1>
          </td>
          <td style="text-align: right;vertical-align: top;width:25%;">
            <div class="input-group">
              <span class="input-group-addon" id="date-auto-sentry">Date</span>
              <input class="form-control" aria-describedby="date-auto-sentry" id='mp_date' onchange="load_page()">
            </div>
<br>
            <div class="input-group">
              <span class="input-group-addon" id="opener-auto-sentry">Manager</span>
              <input class="form-control" aria-describedby="opener-auto-sentry" id='mp_name'>
            </div>
          </td>
        </tr>
      </table>


      <table border="1" id='manager_table' class='ExcelTable2003' width="100%">


        <tr>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;font-size: 25px !important;">Evening Pull</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;font-size: 25px !important;">Morning Pull</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td colspan=2 class='xcl_header' style="text-align: center;font-size: 25px !important;">Shift Report</td>
        </tr>

        <tr>
          <td colspan=2 class='' style="text-align: center;border-bottom: 3px solid black !important;" id='evening_mp_name'></td>
          <td>&nbsp;</td>
          <td colspan=2 class='' style="text-align: center;border-bottom: 3px solid black !important;" id='morning_mp_name'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="ep_input" style="text-align: center;">
            <form id='csv_upload' action="?" method="post" enctype="multipart/form-data">
              <input type="file" class="filestyle" data-buttonText="Upload CSV"  data-badge="false" name="file" id="file" />
              <input type='hidden' name='csvuploadflag' id='csvuploadflag' value='goforit'>
            </form>
          </td>
        </tr>

        <tr>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;">Auto Sentry #1</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;">Auto Sentry #1</td>
          <td>&nbsp;</td>
          <td>Total Sales</td>
          <td class="yellow_bg"><input class="form-control" id='total_sales'></td>
        </tr>

        <tr>
          <td colspan=2 style="text-align: center;border-bottom: 3px solid black !important;" id='evening_as1_accepter'>Accepter</td>
          <td>&nbsp;</td>
          <td id='morning_as1_dispenser' style="text-align: center;border-bottom: 3px solid black !important;">Dispensers</td>
          <td id='morning_as1_accepter' style="text-align: center;border-bottom: 3px solid black !important;">Accepter</td>
          <td>&nbsp;</td>
          <td>Gift Card Sales</td>
          <td class="yellow_bg"><input class="form-control" id='gift_card_sales'></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Gift Card Free (Checks)</td>
          <td class="yellow_bg"><input class="form-control" id='gift_card_free_checks'></td>
        </tr>

        <tr>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;">Auto Sentry #2</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;">Auto Sentry #2</td>
          <td>&nbsp;</td>
          <td>Total Credit Cards</td>
          <td class="yellow_bg"><input class="form-control" id='total_credit_cards'></td>
        </tr>

        <tr>
          <td colspan=2 style="text-align: center;border-bottom: 3px solid black !important;" id='evening_as2_accepter'>Accepter</td>
          <td>&nbsp;</td>
          <td id='morning_as2_dispenser' style="text-align: center;border-bottom: 3px solid black !important;">Dispensers</td>
          <td id='morning_as2_accepter' style="text-align: center;border-bottom: 3px solid black !important;">Accepter</td>
          <td>&nbsp;</td>
          <td>Fleet</td>
          <td class="yellow_bg"><input class="form-control" id='fleet'></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Gift Card Redeemed</td>
          <td class="yellow_bg"><input class="form-control" id='gift_card_redeemed'></td>
        </tr>

        <tr>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;">Auto Sentry #3</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header_jank' style="text-align: center;">Auto Sentry #3</td>
          <td>&nbsp;</td>
          <td>Refunds Due</td>
          <td class="yellow_bg"><input class="form-control" id='refunds_due'></td>
        </tr>

        <tr>
          <td colspan=2 style="text-align: center;border-bottom: 3px solid black !important;" id='evening_as3_accepter'>Accepter</td>
          <td>&nbsp;</td>
          <td id='morning_as3_dispenser' style='text-align: center;border-bottom: 3px solid black !important;'>Dispensers</td>
          <td id='morning_as3_accepter' style='text-align: center;border-bottom: 3px solid black !important;'>Accepter</td>
          <td>&nbsp;</td>
          <td>Payouts</td>
          <td class="yellow_bg"><input class="form-control" id='payouts'></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Total Cash</td>
          <td id='total_cash'></td>
        </tr>

        <tr>
          <td colspan=2 class='xcl_header' style="text-align: center;" id='evening_vaultbag_accepter'>Vault Bag</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header' style="text-align: center;" id='morning_vaultbag_accepter'>Vault Bag</td>
          <td>&nbsp;</td>
          <td>Wash Deposit Amount</td>
          <td id='wash_deposit_is'></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Difference</td>
          <td id='shift_report_difference'></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header' style="text-align: center;" id='morning_pos'>POS</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class='green_bg'>Vending Deposit</td>
          <td class='green_bg' id='vending_deposit'>26.00</td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header' style="text-align: center;" id='morning_vending'>Vending</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header' style="text-align: center;" id='morning_wash_deposit'>Wash Deposit</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td colspan=2 class='' style="text-align: center;">

            <table width=100%>
              <tr>
                <td style="border-top: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-bottom: 1px solid black !important;">
                  <b>Evening Pull Notes</b>
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-top: 1px solid black !important;">
                  <textarea rows=6 class="form-control" id='evening_comments_notes' disabled='disabled'></textarea>
                </td>
              </tr>
            </table>

          </td>
          <td>&nbsp;</td>
          <td colspan=2 class='' style="text-align: center;">

            <table width=100%>
              <tr>
                <td style="border-top: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-bottom: 1px solid black !important;">
                  <b>Morning Pull Notes</b>
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-top: 1px solid black !important;">
                  <textarea rows=6 class="form-control" id='morning_comments_notes' disabled='disabled'></textarea>
                </td>
              </tr>
            </table>

          </td>
          <td>&nbsp;</td>
          <td colspan=2 class='' style="text-align: center;">
            <table width=100%>
              <tr>
                <td style="border-top: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-bottom: 1px solid black !important;">
                  <b>Manager Notes</b>
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-top: 1px solid black !important;">
                  <textarea rows=6 class="form-control" id='manager_comments_notes'></textarea>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <tr>
          <td colspan='8'><hr></td>
        </tr>
        <tr>
          <td colspan=8 class='xcl_header' style="text-align: center;font-size: 25px !important;">Troubleshooting Guide</td>
        </tr>

        <tr>
          <td class='xcl_header' style="text-align: center;">Bills</td>
          <td class='xcl_header' style="text-align: center;">Expected Deposit</td>
          <td class='xcl_header' style="text-align: center;">&nbsp;</td>
          <td class='xcl_header' style="text-align: center;" colspan=2>Ending Vault Bag</td>
          <td class='xcl_header' style="text-align: center;">Put In</td>
          <td class='xcl_header' style="text-align: center;">Taken Out</td>
          <td class='xcl_header' style="text-align: center;">Missing Bills</td>
        </tr>

        <tr>
          <td style="text-align: right;">$1</td>
          <td class='yellow_bg' style="text-align: right;" id='troubleshooting_bill1_expdep'></td>
          <td style="text-align: right;" id='troubleshooting_bill1_emptycol'></td>
          <td colspan=2 class='yellow_bg' style="text-align: right;" id='troubleshooting_bill1_endingvaultbag'></td>
          <td style="background-color: #219FEC !important;"><input class="form-control" style="background-color: #219FEC !important;text-align: right;color: white;" id='troubleshooting_bill1_putin'></td>
          <td style="background-color: #5C198F !important;"><input class="form-control" style="background-color: #5C198F !important;text-align: right;color: white;" id='troubleshooting_bill1_takenout'></td>
          <td style="background-color: #B00005 !important;text-align: right;color: white;" id='troubleshooting_bill1_missingbills'></td>
        </tr>
        <tr>
          <td style="text-align: right;">$5</td>
          <td class='yellow_bg' style="text-align: right;" id='troubleshooting_bill5_expdep'></td>
          <td style="text-align: right;" id='troubleshooting_bill5_emptycol'></td>
          <td colspan=2 class='yellow_bg' style="text-align: right;" id='troubleshooting_bill5_endingvaultbag'></td>
          <td style="background-color: #219FEC !important;"><input class="form-control" style="background-color: #219FEC !important;text-align: right;color: white;" id='troubleshooting_bill5_putin'></td>
          <td style="background-color: #5C198F !important;"><input class="form-control" style="background-color: #5C198F !important;text-align: right;color: white;" id='troubleshooting_bill5_takenout'></td>
          <td style="background-color: #B00005 !important;text-align: right;color: white;" id='troubleshooting_bill5_missingbills'></td>
        </tr>
        <tr>
          <td style="text-align: right;">$10</td>
          <td class='yellow_bg' style="text-align: right;" id='troubleshooting_bill10_expdep'></td>
          <td style="text-align: right;" id='troubleshooting_bill10_emptycol'></td>
          <td colspan=2 class='yellow_bg' style="text-align: right;" id='troubleshooting_bill10_endingvaultbag'></td>
          <td style="background-color: #219FEC !important;"><input class="form-control" style="background-color: #219FEC !important;text-align: right;color: white;" id='troubleshooting_bill10_putin'></td>
          <td style="background-color: #5C198F !important;"><input class="form-control" style="background-color: #5C198F !important;text-align: right;color: white;" id='troubleshooting_bill10_takenout'></td>
          <td style="background-color: #B00005 !important;text-align: right;color: white;" id='troubleshooting_bill10_missingbills'></td>
        </tr>
        <tr>
          <td style="text-align: right;">$20</td>
          <td class='yellow_bg' style="text-align: right;" id='troubleshooting_bill20_expdep'></td>
          <td style="text-align: right;" id='troubleshooting_bill20_emptycol'></td>
          <td colspan=2 class='yellow_bg' style="text-align: right;" id='troubleshooting_bill20_endingvaultbag'></td>
          <td style="background-color: #219FEC !important;"><input class="form-control" style="background-color: #219FEC !important;text-align: right;color: white;" id='troubleshooting_bill20_putin'></td>
          <td style="background-color: #5C198F !important;"><input class="form-control" style="background-color: #5C198F !important;text-align: right;color: white;" id='troubleshooting_bill20_takenout'></td>
          <td style="background-color: #B00005 !important;text-align: right;color: white;" id='troubleshooting_bill20_missingbills'></td>
        </tr>
        <tr>
          <td style="text-align: right;">Total Coin</td>
          <td class='yellow_bg' style="text-align: right;" id='troubleshooting_coin_expdep'></td>
          <td style="text-align: right;" id='troubleshooting_coin_emptycol'></td>
          <td colspan=2 class='yellow_bg' style="text-align: right;" id='troubleshooting_coin_endingvaultbag'></td>
          <td style="background-color: #219FEC !important;"><input class="form-control" style="background-color: #219FEC !important;text-align: right;color: white;" id='troubleshooting_coin_putin'></td>
          <td style="background-color: #5C198F !important;"><input class="form-control" style="background-color: #5C198F !important;text-align: right;color: white;" id='troubleshooting_coin_takenout'></td>
          <td style="background-color: #B00005 !important;text-align: right;color: white;" id='troubleshooting_coin_missingbills'></td>
        </tr>

      </table>



      </div>

    </div>
<?
require("global_footer.php");
?>
