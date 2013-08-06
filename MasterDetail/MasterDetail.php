<?php
// Made the event grid sort DESC
session_start(); //  Needed to keep state through refresh
$KoolControlsFolder= "..";
require $KoolControlsFolder."/KoolAjax/koolajax.php";
$koolajax->scriptFolder = $KoolControlsFolder."/KoolAjax";

require $KoolControlsFolder."/KoolGrid/koolgrid.php";
 //require $KoolControlsFolder."/login.php";
 require $KoolControlsFolder."/../../login.php";


$db_con = mysql_connect($db_hostname,$db_username,$db_password);
mysql_select_db($db_database);
// =================
$ds_event = new MySQLDataSource($db_con);//This $db_con link has been created inside KoolPHPSuite/Resources/runexample.php
$ds_event->SelectCommand = "select event_id,utc,mac,latitude,longitude,mean_sealevel,week_number,rising_edge,analog from events order by event_id DESC";


$grid_event = new KoolGrid("grid_event");
$grid_event->KeepViewStateInSession = true; 
$grid_event->scriptFolder = $KoolControlsFolder."/KoolGrid";
$grid_event->styleFolder="default";

$grid_event->RowAlternative = true;
$grid_event->AjaxEnabled = true;
$grid_event->AllowSelecting = true;
$grid_event->AllowFiltering = true;
$grid_event->AllowResizing = true;
$grid_event->KeepGridRefresh = true;
//$grid_event->Width = "900px";
$grid_event->Width = "85%";
$grid_event->AllowSorting = true;//Enable sorting for all rows;
$grid_event->AllowScrolling = true;
$grid_event->Height = "500px";
 $grid->MasterTable->VirtualScrolling = true;
 $grid->MasterTable->Height = "400px";
$grid->MasterTable->ColumnWidth = "80px";

$grid_event->AjaxLoadingImage =  $KoolControlsFolder."/KoolAjax/loading/5.gif";

$grid_event->MasterTable->DataSource = $ds_event;
$grid_event->MasterTable->AutoGenerateColumns = true;

$grid_event->MasterTable->Pager = new GridPrevNextAndNumericPager();
$grid_event->MasterTable->Pager->ShowPageSize = true;
$grid_event->ClientSettings->ClientEvents["OnRowSelect"] = "Handle_OnRowSelect";

 
$grid_event->Process();
 

$ds_pixel = new MySQLDataSource($db_con);//This $db_con link has been created inside KoolPHPSuite/Resources/runexample.php

if(isset($_POST["event_selected"]))
{
  // $ds_pixel->SelectCommand = "select pixel_id,org_id,mac,birthday from pixels where mac=".$_POST["mac"];
    $ds_pixel->SelectCommand = "select pixel_id,org_id,mac,birthday,first_light,last_seen,pixelType,shieldType,note  from pixels where mac='".$_POST["mac"]."'";
   $_SESSION["mac"] = $_POST["mac"];
   
}
else
{
    if(!$koolajax->isCallback)
    {
       
        $_rows = $grid_event->GetInstanceMasterTable()->GetInstanceRows();
        $_rows[0]->Selected = true;
    
        $ds_pixel->SelectCommand = "select pixel_id,org_id,mac,birthday,first_light,last_seen,pixelType,shieldType,note  from pixels where mac='".$_rows[0]->DataItem["mac"]."'";
       
    }
    else
    {
         // $ds_pixel->SelectCommand = "select pixel_id,org_id,mac,birthday from pixels where mac = ".$_SESSION["mac"]; 
   
        $ds_pixel->SelectCommand = "select pixel_id,org_id,mac,birthday,first_light,last_seen,pixelType,shieldType,note from pixels where mac = '".$_SESSION["mac"]."'";
       
    }
}

$grid_pixel= new KoolGrid("grid_pixel");
$grid_pixel->scriptFolder = $KoolControlsFolder."/KoolGrid";
$grid_pixel->RowAlternative = true;
$grid_pixel->styleFolder="default";
$grid_pixel->Width = "655px";
$grid_pixel->AjaxEnabled = true;
$grid_pixel->AjaxLoadingImage =  $KoolControlsFolder."/KoolAjax/loading/5.gif";
$grid_pixel->Width = "1200px";
$grid_pixel->MasterTable->DataSource = $ds_pixel;
$grid_pixel->MasterTable->AutoGenerateColumns = true;

//$grid_pixel->MasterTable->Pager = new GridPrevNextAndNumericPager();
$grid_pixel->Process();
?>


<html>


<!-- #BeginTemplate "../../../CosmicRays/CosmicRays.dwt" -->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
    <!.. #BeginEditable "doctitle" ..>
    <title>Cosmic Rays</title>
    <!.. #EndEditable ..>
    <!--mstheme--><link rel="stylesheet" type="text/css" href="../../../_themes/studio/stud1110.css"><meta name="Microsoft Theme" content="studio 1110">
</head>

<body>
 
    <table border="0" width="90%" height="800">
        <tr>
            <td height="83">

                <p align="center">
                    <!--webbot bot="Navigation" s-type="banner" s-orientation="horizontal" s-rendering="graphics" startspan --><!--webbot bot="Navigation" i-checksum="0" endspan -->
                </p>
                <p align="center">
                    <!--webbot bot="Navigation" s-orientation="horizontal" s-rendering="graphics" s-type="siblings" b-include-home="FALSE" b-include-up="TRUE" S-Theme="boldstri 0100" startspan --><!--webbot bot="Navigation" i-checksum="0" endspan -->
                </p>
                <p align="center">
                    <!--webbot bot="Navigation" S-Orientation="horizontal" S-Rendering="graphics" S-Type="children" B-Include-Home="FALSE" B-Include-Up="FALSE" startspan --><!--webbot bot="Navigation" i-checksum="0" endspan -->
                </p>
            </td>
        </tr>
        <tr>
            <td height="100%" valign="top" align="center" width="78%">

                <!-- #BeginEditable "Main" -->&nbsp;

==This page is a work in progress=

                <form id="form1" method="post">
                    <?php echo $koolajax->Render();?>

                    <script type="text/javascript">
		function Handle_OnRowSelect(sender,args)
		{
        //document.write("Handle_ONRowSelect..");
			//Prepare to refresh the grid_event.
			var _row = args["Row"];
			grid_pixel.attachData("event_selected",1);
            grid_pixel.attachData("mac",_row.getDataItem()["mac"]);
		 
			grid_pixel.refresh();
			grid_pixel.commit();
		}
                    </script>

                    <div style="margin-top:10px;font-weight:bold;">Events:</div>
                    <?php echo $grid_event->Render();?>
                    
                    
                    
                    <div style="margin-top:10px;font-weight:bold;">Pixel 
						Information:</div>
                    <?php echo $grid_pixel->Render();?>

                </form>


                =====================================================
                <!-- #EndEditable -->
            </td>
        </tr>
        <tr>
            <td>

                <p align="center">
                    <!--webbot bot="Navigation" s-orientation="horizontal" s-rendering="graphics" s-type="children" b-include-home="FALSE" b-include-up="TRUE" startspan --><!--webbot bot="Navigation" i-checksum="0" endspan -->
                </p>
            </td>
        </tr>
    </table>

</body>

<!-- #EndTemplate -->

</html>
