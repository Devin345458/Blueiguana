<?
require("global_header.php");
$morning_pull="active";
require("middle_header.php");
?>
      <link rel="stylesheet" type="text/css" href="css/morning_pull.css">
      <script>


function format_money(num3) {
  var numb = parseFloat(Math.round(num3 * 100) / 100).toFixed(2);
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
    input_data['comments_notes'] = jQuery('#comments_notes').val();
    return input_data;
}

function get_remote_data() {
  var the_date = new Date($('#mp_date').val());

  var post_data = {};
  post_data['year'] = the_date.getFullYear();
  post_data['month'] = the_date.getMonth()+1;
  post_data['day'] = the_date.getDate();
  $.ajax({
      type: 'POST',
      url: 'api.php?action=get_morning_pull',
      data: post_data,
      dataType: 'json',
      success: function (full_data) {
        existing_data = JSON.parse(full_data['morning_pull']);
        jQuery("#morning_pull_table").find('input').val('');
        for (var key in existing_data) {
          jQuery("#"+key).val(existing_data[key]);
        }
        jQuery('#comments_notes').val(existing_data['comments_notes']);

        evening_data = JSON.parse(full_data['evening_pull']);
        jQuery("#as1_pull1_1").val(evening_data["as1_pull1_1"]);
        jQuery("#as1_pull1_5").val(evening_data["as1_pull1_5"]);
        jQuery("#as1_pull1_10").val(evening_data["as1_pull1_10"]);
        jQuery("#as1_pull1_20").val(evening_data["as1_pull1_20"]);

        jQuery("#as2_pull1_1").val(evening_data["as2_pull1_1"]);
        jQuery("#as2_pull1_5").val(evening_data["as2_pull1_5"]);
        jQuery("#as2_pull1_10").val(evening_data["as2_pull1_10"]);
        jQuery("#as2_pull1_20").val(evening_data["as2_pull1_20"]);

        jQuery("#as3_pull1_1").val(evening_data["as3_pull1_1"]);
        jQuery("#as3_pull1_5").val(evening_data["as3_pull1_5"]);
        jQuery("#as3_pull1_10").val(evening_data["as3_pull1_10"]);
        jQuery("#as3_pull1_20").val(evening_data["as3_pull1_20"]);


        update_calculations();
        console.log("get_morning_pull", existing_data);
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
      url: 'api.php?action=save_morning_pull',
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
  var total_as1_pull1 = get_val("#as1_pull1_1") + get_val("#as1_pull1_5")*5 + get_val("#as1_pull1_10")*10 + get_val("#as1_pull1_20")*20;
  $("#total_as1_pull1").html(format_money(total_as1_pull1));

  var total_as2_pull1 = get_val("#as2_pull1_1") + get_val("#as2_pull1_5")*5 + get_val("#as2_pull1_10")*10 + get_val("#as2_pull1_20")*20;
  $("#total_as2_pull1").html(format_money(total_as2_pull1));

  var total_as3_pull1 = get_val("#as3_pull1_1") + get_val("#as3_pull1_5")*5 + get_val("#as3_pull1_10")*10 + get_val("#as3_pull1_20")*20;
  $("#total_as3_pull1").html(format_money(total_as3_pull1));

  var total_as1_pull2 = get_val("#as1_pull2_1") + get_val("#as1_pull2_5")*5 + get_val("#as1_pull2_10")*10 + get_val("#as1_pull2_20")*20;
  $("#total_as1_pull2").html(format_money(total_as1_pull2));

  var total_as2_pull2 = get_val("#as2_pull2_1") + get_val("#as2_pull2_5")*5 + get_val("#as2_pull2_10")*10 + get_val("#as2_pull2_20")*20;
  $("#total_as2_pull2").html(format_money(total_as2_pull2));

  var total_as3_pull2 = get_val("#as3_pull2_1") + get_val("#as3_pull2_5")*5 + get_val("#as3_pull2_10")*10 + get_val("#as3_pull2_20")*20;
  $("#total_as3_pull2").html(format_money(total_as3_pull2));

  //Vault
  var vault_total = get_val("#vault_bag_1") + get_val("#vault_bag_5")*5 + get_val("#vault_bag_10")*10 + get_val("#vault_bag_20")*20 + get_val("#vault_bag_50")*50 + get_val("#vault_bag_100")*100;
  var vault_results = vault_total - 300;
  if (vault_results < 0) {
    vault_results = "<span style='color:red;font-weight:bold;'>Add "+ format_money(vault_results*-1)+"</span>";
  }
  else if (vault_results > 0) {
    vault_results = "<span style='color:blue;font-weight:bold;'>Remove "+ format_money(vault_results)+"</span>";
  }
  else {
    vault_results = "<span style='color:blue;font-weight:bold;'>Even</span>";
  }
  $('#vault_bag_results').html(vault_results);
  $("#total_vault_bag").attr('data-amount', vault_total);

  //POS
  var pos_total = get_val("#pos_1") + get_val("#pos_5")*5 + get_val("#pos_10")*10 + get_val("#pos_20")*20 + get_val("#pos_50")*50 + get_val("#pos_100")*100 + get_val("#pos_coins");
  $("#total_pos").attr('data-amount', pos_total);

  //Vending
  var vending_total = get_val("#vending_1") + get_val("#vending_5")*5 + get_val("#vending_coins");
  $("#total_vending").attr('data-amount', vending_total);

  //Wash Deposit Should Be
  var wash_dep_should = get_val("#as1_1billdispenser") + get_val("#as1_5billdispenser")*5 + get_val("#as2_1billdispenser") + get_val("#as2_5billdispenser")*5 + get_val("#as3_1billdispenser") + get_val("#as3_5billdispenser")*5;
  wash_dep_should += get_htmlval("#total_as1_pull1") + get_htmlval("#total_as2_pull1") + get_htmlval("#total_as3_pull1")
  wash_dep_should += get_htmlval("#total_as1_pull2") + get_htmlval("#total_as2_pull2") + get_htmlval("#total_as3_pull2")
  wash_dep_should += get_attrval("#total_vault_bag") + get_attrval("#total_pos");

  wash_dep_should = wash_dep_should - 2500
  $("#wash_deposit_should_be").html(format_money(wash_dep_should));

  //Wash Deposit Is
  var wash_dep_is = get_val("#wash_deposit_1") + get_val("#wash_deposit_5")*5 + get_val("#wash_deposit_10")*10 + get_val("#wash_deposit_20")*20 + get_val("#wash_deposit_50")*50 + get_val("#wash_deposit_100")*100 + get_val("#wash_deposit_coins")
  $("#wash_deposit_is").html(format_money(wash_dep_is));

  wash_deposit_difference = Math.abs(wash_dep_should-wash_dep_is);

  if (wash_deposit_difference <= 1) {
    $('#wash_deposit_is').addClass('green_bg');
    $('#wash_deposit_is').removeClass('red_bg');
    $('#wash_deposit_is').removeClass('white_bg');
  }
  else if (wash_deposit_difference <= 5) {
    $('#wash_deposit_is').addClass('white_bg');
    $('#wash_deposit_is').removeClass('green_bg');
    $('#wash_deposit_is').removeClass('red_bg');
  }
  else {
    $('#wash_deposit_is').addClass('red_bg');
    $('#wash_deposit_is').removeClass('green_bg');
    $('#wash_deposit_is').removeClass('white_bg');
  }

  //Vending Deposit Should Be
  var vending_dep_should = get_val("#vending_1") + get_val("#vending_5")*5 + get_val("#vending_coins")
  $("#vending_deposit_should_be").html(format_money(vending_dep_should));

  //Vending Deposit Is
  var vending_dep_is = get_val("#vending_deposit_1") + get_val("#vending_deposit_5")*5 + get_val("#vending_deposit_10")*10 + get_val("#vending_deposit_20")*20 + get_val("#vending_deposit_50")*50 + get_val("#vending_deposit_100")*100 + get_val("#vending_deposit_coins")
  $("#vending_deposit_is").html(format_money(vending_dep_is));

  vending_deposit_difference = Math.abs(vending_dep_should-vending_dep_is);

  if (vending_deposit_difference <= 1) {
    $('#vending_deposit_is').addClass('green_bg');
    $('#vending_deposit_is').removeClass('red_bg');
    $('#vending_deposit_is').removeClass('white_bg');
  }
  else if (vending_deposit_difference <= 5) {
    $('#vending_deposit_is').addClass('white_bg');
    $('#vending_deposit_is').removeClass('green_bg');
    $('#vending_deposit_is').removeClass('red_bg');
  }
  else {
    $('#vending_deposit_is').addClass('red_bg');
    $('#vending_deposit_is').removeClass('green_bg');
    $('#vending_deposit_is').removeClass('white_bg');
  }

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

    $("#morning_pull_table").find("input").keyup(function () {
     update_calculations(true);
    });
    $("#mp_name").keyup(function () {
     update_calculations(true);
    });
    $("#comments_notes").keyup(function () {
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
              <span class="input-group-addon" id="opener-auto-sentry">Name</span>
              <input class="form-control" aria-describedby="opener-auto-sentry" id='mp_name'>
            </div>
          </td>
        </tr>
      </table>


      <table border="1" id='morning_pull_table' class='ExcelTable2003'>
        <tr>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header'>Auto Sentry #1</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header'>Auto Sentry #2</td>
          <td>&nbsp;</td>
          <td colspan=2 class='xcl_header'>Auto Sentry #3</td>
          <td>&nbsp;</td>
          <td class='xcl_header'>Vault Bag</td>
          <td>&nbsp;</td>
          <td class='xcl_header'>POS</td>
          <td>&nbsp;</td>
          <td class='xcl_header'>Vending</td>
        </tr>


        <tr>
          <td>$1 Bill Dispenser</td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as1_1billdispenser'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as2_1billdispenser'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as3_1billdispenser'></td>
          <td>&nbsp;</td>
          <td rowspan=2 style="text-align:center;" id='vault_bag_results'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$5 Bill Dispenser</td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as1_5billdispenser'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as2_5billdispenser'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as3_5billdispenser'></td>
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
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
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
          <td class='bold'>Bill Accepters:</td>
          <td class='xcl_header'>Pull 1</td>
          <td class='xcl_header'>Pull 2</td>
          <td>&nbsp;</td>
          <td class='xcl_header'>Pull 1</td>
          <td class='xcl_header'>Pull 2</td>
          <td>&nbsp;</td>
          <td class='xcl_header'>Pull 1</td>
          <td class='xcl_header'>Pull 2</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$1</td>
          <td class="ep_input"><input class="form-control" id='as1_pull1_1' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as1_pull2_1'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as2_pull1_1' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as2_pull2_1'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as3_pull1_1' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as3_pull2_1'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='vault_bag_1'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_1'></td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_1'></td>
        </tr>

        <tr>
          <td>$5</td>
          <td class="ep_input"><input class="form-control" id='as1_pull1_5' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as1_pull2_5'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as2_pull1_5' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as2_pull2_5'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as3_pull1_5' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as3_pull2_5'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='vault_bag_5'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_5'></td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_5'></td>
        </tr>

        <tr>
          <td>$10</td>
          <td class="ep_input"><input class="form-control" id='as1_pull1_10' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as1_pull2_10'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as2_pull1_10' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as2_pull2_10'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as3_pull1_10' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as3_pull2_10'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='vault_bag_10'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_10'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$20</td>
          <td class="ep_input"><input class="form-control" id='as1_pull1_20' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as1_pull2_20'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as2_pull1_20' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as2_pull2_20'></td>
          <td>&nbsp;</td>
          <td class="ep_input"><input class="form-control" id='as3_pull1_20' disabled='disabled'></td>
          <td class="yellow_bg"><input class="form-control" id='as3_pull2_20'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='vault_bag_20'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_20'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$50</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='vault_bag_50'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_50'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$100</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='vault_bag_100'></td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_100'></td>
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
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>Total Coins</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="yellow_bg"><input class="form-control" id='pos_coins' placeholder="$0.00"></td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_coins' placeholder="$0.00"></td>



        </tr>

        <tr>
          <td class='bold'>Total</td>
          <td class='bold border_top_heavy' id='total_as1_pull1'>$0.00</td>
          <td class='bold border_top_heavy' id='total_as1_pull2'>$0.00</td>
          <td class='bold'>&nbsp;</td>
          <td class='bold border_top_heavy' id='total_as2_pull1'>$0.00</td>
          <td class='bold border_top_heavy' id='total_as2_pull2'>$0.00</td>
          <td class='bold'>&nbsp;</td>
          <td class='bold border_top_heavy' id='total_as3_pull1'>$0.00</td>
          <td class='bold border_top_heavy' id='total_as3_pull2'>$0.00</td>
          <td class='bold'>&nbsp;</td>
          <td class='bold' id='total_vault_bag' data-amount="0">&nbsp;</td>
          <td class='bold'>&nbsp;</td>
          <td class='bold' id='total_pos'>&nbsp;</td>
          <td class='bold'>&nbsp;</td>
          <td class='bold' id='total_vending'>&nbsp;</td>
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
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>


        <tr>
          <td colspan=5 class='xcl_header'>Wash Deposit</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan=4 class='xcl_header'>Vending Deposit</td>
          <td>&nbsp;</td>
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
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan=3 rowspan=10>
            <table>
              <tr>
                <td style="border-top: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-bottom: 1px solid black !important;">
                  <b>Comments / Notes:</b>
                </td>
              </tr>
              <tr>
                <td style="border-bottom: 2px solid black !important;border-left: 2px solid black !important;border-right: 2px solid black !important;border-top: 1px solid black !important;">
                  <textarea rows=12 class="form-control" id='comments_notes'></textarea>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <tr>
          <td>Bills:</td>
          <td>&nbsp;</td>
          <td colspan=3 class='should_be'>Wash Deposit Should Be:</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan=3 class='should_be'>Vending Deposit Should Be:</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$1</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_1'></td>
          <td colspan=3 id='wash_deposit_should_be' class='should_be'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_1'></td>
          <td colspan=3 id='vending_deposit_should_be' class='should_be'></td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$5</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_5'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_5'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$10</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_10'></td>
          <td colspan=3 class='deposit_is_label'>Wash Deposit Is:</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_10'></td>
          <td colspan=3 class='deposit_is_label'>Vending Deposit Is:</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>$20</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_20'></td>
          <td colspan=3 class='deposit_is_value green_bg' id='wash_deposit_is'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_20'></td>
          <td colspan=3 class='deposit_is_value green_bg' id='vending_deposit_is'></td>
          <td>&nbsp;</td>
        </tr>


        <tr>
          <td>$50</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_50'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_50'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>


        <tr>
          <td>$100</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_100'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_100'></td>
          <td>&nbsp;</td>
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
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td>Total Coins</td>
          <td class="yellow_bg"><input class="form-control" id='wash_deposit_coins' placeholder="$0.00"></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="green_bg"><input class="form-control" id='vending_deposit_coins' placeholder="$0.00"></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>


      </table>



      </div>

    </div>
<?
require("global_footer.php");
?>
