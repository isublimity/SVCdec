<?php
$MAINMSG=";\n;\tSVCdec vers.0.1.0 (25ag2/10)\n;\n;\tsublimity - vcds - clb - file - decoder-encoder\n;\n;\n";
function ArgvToGlobal($GlobalsSet=true)
    {
    global $argv;
    $arr=array();
    foreach ($argv as $arg)
        {
        if (substr($arg,0,1)=='-')
            {
            $k=substr($arg,1);
            $k=explode(':',$k);
            $arr[$k[0]]=(empty($k[1])?true:$k[1]);
            }
        }
    if ($GlobalsSet) foreach ($arr as $key=>$val) $GLOBALS['arg_'.$key]=$val;
    return $arr;
    }
function findKeyPosValues($arr=array())
{
    echo ";\n;\n;\tFIND METHOD VARS by brt lines\n";
    echo ";\tInput counts array :".sizeof($arr)."\n";
    for ($p=0;$p<256;$p++)
    {
        
        for ($z=0;$z<256;$z++)
        {
            $count=0;
            foreach ($arr as $num=>$need)
            if ($num>0)
            {
                $result=((  $num%16  ) *$p +$z)%256;
                if ($result==$need) $count++;
            }//for arr
            if ($count>(sizeof($arr)/2))
            {
                echo ";\tFind varianz:\t\tZ: $z ,\t\tP: $p \t count : $count; \n";
                $a[$count]=array($z,$p);
            }
        }//for $z
        
    }//for $p

    if (sizeof($a))
        {
            $max=max(array_keys($a));
            $res=array('p'=>$a[$max][1],'z'=>$a[$max][0]);
            echo ";\tUse count := $max\n;\tP = {$res['p']}\n;\tZ = {$res['z']}\n;\n;\n";
            return $res;
        }
        else
            {
                echo "; \t !!! ERROR : not find key vars \n;\n;\n";
            }
    return array('p'=>-1,'z'=>-1);
}//functions
function getKeyPosManual($num,$p,$z)
    {
        $ret=((  $num%16  ) *$p +$z)%256;
        return $ret;
    }
function getKeyPosV3($num)
    {
        $p=3;
        $z=233;
        //$ret=(($p) + 2 * (($lineNum) % 0xF))%256;
        $ret=((  $num%16  ) *$p +$z)%256;
        return $ret;
    }
function getKeyPosOld($lineNum)
{
    $p=3;
    $z=250;
    $ret=((  $lineNum%16  ) *$p +$z)%256;
    return $ret;
 /*
    //$methodNum=2;
    $methodNum=3;
    if ($lineNum==0) return 250;
    if ($lineNum==17) return 253;
    if ($lineNum==51) return 3;
    //if ($lineNum==68) return 138;
    if ($lineNum==68) return 6;
    if ($lineNum==85) return 9;
    //v5 = (((v4 [158854]) + v11) & 0xFF) + 2 * ((v4 + 20) & 0xF);
    if (($lineNum>150) && ($lineNum<306))
        {
            if ($lineNum==289) return 253;
            if ($lineNum==272) return 250;
            if ($lineNum==255) return 39;
            if ($lineNum==238) return 36;
            if ($lineNum==221) return 33;
            if ($lineNum==204) return 30;
            if ($lineNum==187) return 27;
            if ($lineNum==170) return 24;
            if (($lineNum/17)==floor($lineNum/17)) return 21;
        }
    if (($lineNum/16)==floor($lineNum/16)) return 250;

    $lineNum=$lineNum%16;//-16*(floor($lineNum/16));
    $retPosint=($lineNum*$methodNum-6);
    if ($retPosint==-3) return 253;
    if ($retPosint<0)
        {
        echo "[$lineNum / $retPosint]";
            return 253;
        }
    return $retPosint;

  */
}
function getKeyPosNew($lineNum)
{
    $p=2;
    $z=250;
    $ret=((  $lineNum%16  ) *$p +$z)%256;
    return $ret;
    /*
    $methodNum=2;
    $minus=6;
    //$methodNum=3;
    if ($lineNum==0) return 250;
    //if ($lineNum==17) return 252;
    //if ($lineNum==51) return 0;
    //if ($lineNum==68) return 138;
    //if ($lineNum==68) return 2;
    //if ($lineNum==85) return 33;
    //v5 = (((v4 [158854]) + v11) & 0xFF) + 2 * ((v4 + 20) & 0xF);
    if (($lineNum/16)==floor($lineNum/16)) return 250;

    if (($lineNum>84) )
        {
            //$minus=8;
            //if (($lineNum/16)==floor($lineNum/16)) return 24;
        }
        else
        {
        }
    if (($lineNum>150) && ($lineNum<306))
        {
        }
    //
    $lineNum=$lineNum%16;//-16*(floor($lineNum/16));
    //echo "[$lineNum]";
    $retPosint=($lineNum*$methodNum-$minus);
    //echo "($retPosint)";
    if ($retPosint==-6) return 250;
    if ($retPosint==-2) { return 254; }
    if ($retPosint<0)   { return 252; }
    return $retPosint;

     */
}
function clbtoarray($fn,$isNorm=false)
{
        $data=file_get_contents($fn);
        echo ";Read from file ".strlen($data).' bytes...'."\n";
        if ($isNorm) 
            $arr=explode(chr(10),$data);
        else
            $arr=explode(chr(0).chr(10),$data);
        $c=array();
        foreach ($arr as $ln=>$txt)
        {
                $c[]=$txt;//substr($txt,0,strlen($txt)-1);
        }
        return $c;
}
function encodeline($key,$clbTxt,$keyPosit)
{
    $ln='';
    $d=0;
    for($f=0;$f<strlen($clbTxt);$f++)
        {
            $pos=$keyPosit+$f;
            if (empty($key[$pos])) die("\n\n\nend key :$pos: codes:".$ln);
            if ($pos>255)
                {
                    if ($d>255) $d=0;
                    $pos=$d;$d++;
                }
            $cch=ord($key[$pos]);$cch=$cch|128;
            $char=ord($clbTxt[$f]);
            //$result=( $cch ^ ($char - $cch)  & 255 ) ;
            $result=(($cch^($char)&255)+$cch)&255;
            
            //$decr=($key^($clb-$key)&255);
						//$clb=(($key^($decr)&255)+$key)&255;

            $ln.=chr($result);
        }
        return $ln;
}
function decodeLine($key,$keyPosit,$clbTxt)
{
    // -1 can`t decode
    //
    $ln='';
    $d=0;
    for($f=0;$f<strlen($clbTxt);$f++)
        {
            $pos=$keyPosit+$f;
            if (empty($key[$pos])) die("\n\n\nend key :$pos: codes:".$ln);
            if ($pos>255)
                {
                    if ($d>255) $d=0;
                    $pos=$d;$d++;
                }
            $cch=ord($key[$pos]);$cch=$cch|128;
            $char=ord($clbTxt[$f]);
            $result=( $cch ^ ($char - $cch)  & 255 ) ;
            if ($result<9 || ($result>=14 && $result<23 ) || $result>256 )
            {
                $bad=1;
                $ln.=';['.$result.']';
            }
            else
            $ln.=chr($result);
        }//for f
    return $ln;
}
function findDecodeLine($keycode,$crline,$flagBrut=0,$lang='de',$showall=false,$fast=false)
{
    $maxresult=170;
    if ($lang=='de') $maxresult=245;
    if ($lang=='ru') $maxresult=245;
    $lines=array();
    $ln='';
    $lines=array();
    if (strlen($crline)<2) return array('x'=>';');
    for ($w=0;$w<256;$w++)
    {
        if ($fast) if ($w>20 && $w<90) $w=240;
        $ln='';
        $bad=0;
        $d=0;
        for($f=0;$f<strlen($crline);$f++)
        {
            $pos=$w+$f;
            if (empty($keycode[$pos])) die("\n\n\nend key :$pos: codes:".$ln);
            if (($pos)>255)
                {
                    if ($d>255) $d=0;
                    $pos=$d;$d++;
                }
            $cch=ord($keycode[$pos]);$cch=$cch|128;
            $char=ord($crline[$f]);
            $result=( $cch ^ ($char - $cch)  & 255 ) ;
            if ($result<11 || ($result>=12 && $result<23 )
                   || ($result>175 && $result<224) || ($result>$maxresult && $result<245 ) )
            {
                $bad=1;
                //$ln.='['.$result.']';
                if ($showall==1) $ln.=chr($result);
                    else $ln.=' ';
            }
            else
            {
                $ln.=chr($result);
            }
        }//for f
        //$lines[$bad][$w]=$ln;
        if ($flagBrut)
            {
                if (!$bad)   $lines[$w]=$ln;
            }
            else
            {
                $lines[$w]=$ln;
            }
    }//for w
    return $lines;
}
function chkLine($msg)
    {
        $b=0;for ($f=0;$f<strlen($msg)-1;$f++)
                if (ord($msg[$f])<12)
                        $b=1;
        return $b;
    }
function trimLine($msg)
    {
        $b=0;
        for ($f=0;$f<strlen($msg);$f++)
            {
                if (ord($msg[$f])<16)
                    {
                        $msg[$f]='_';
                        $b=1;
                    }
            }
        return ($b?'!!!':'').$msg;
    }
function tvar($val,$pos=5)
{
    return $val.str_repeat(" ",$pos-strlen($val));
}
function dumpLine($lineNum,$clbTxt,$pos,$lnText,$flag='',$trueTxt='')
{
    echo ";\t".tvar($lineNum).tvar($pos).tvar($flag);
    if (trim($trueTxt)==trim($lnText)) echo "<+>";
    echo ":\t$lnText\n";
}
// ------------------------------------------------------------------
// ------------------------------------------------------------------

$key=array(
    144,666,476,320,1160,1998,640,2010,222,672,484,320,970,1980,2000,960,160,582,460,1160,1010,180,200,2580,130,426,180,670,790,1386,640,3150,230,192,388,320,1120,2052,2220,3090,228,582,436,320,1160,1872,1940,3480,64,684,468,1100,1150,576,2220,3300,64,462,420,990,1140,1998,2300,3330,204,696,128,870,1050,1980,2000,3330,238,690,184,100,730,1980,640,3330,228,600,404,1140,320,2088,2220,960,224,606,456,1020,1110,2052,2180,960,198,606,456,1160,970,1890,2200,960,204,702,440,990,1160,1890,2220,3300,230,264,128,1210,1110,2106,640,3270,194,726,128,1100,1010,1818,2000,960,232,666,128,1170,1150,1818,200,3480,208,606,128,670,1110,2016,2420,960,194,660,400,320,800,1746,2300,3480,202,192,408,1170,1100,1782,2320,3150,222,660,460,460,100,180,1780,3330,234,684,128,1090,1110,2106,2300,3030,64,666,456,320,1120,1998,2100,3300,232,630,440,1030,320,1800,2020,3540,210,594,404,320,1090,1746,2420,960,220,666,464,320,1080,1998,2220,3210,64,606,480,970,990,2088,2160,3630,64,648,420,1070,1010,576,2320,3120,202,192,444,1100,1010,576,1960,3030,216,666,476,460,100,1314,2040,960,210,696,128,1000,1110,1818,2300,960,220,666,464,440,320,2016,2160,3030,194,690,404,320,1150,1818,2020,960,232,624,404,320,1110,2142,2200,3030,228,234,460,320,1090,1746,2200,3510,194,648,128,1020,1110,2052,640,3630,222,702,456,320,1090,1998,2340,3450,202,192,444,1140,320,1782,2220,3270,224,702,464,1010,1140,576,2320,3330,64,612,420,1100,1000,576,2220,3510,232,60,416,1110,1190,576,2320,3330,64,654,444,1180,1010,576,2320,3120,202,192,396,1170,1140,2070,2220,3420,64,582,440,1000,320,2142,2080,3030,228,606,128,1160,1040,1818,640,3240,202,612,464,320,970,1980,2000,960,228,630,412,1040,1160,576,2180,3330,234,690,404,320,980,2106,2320,3480,222,660,460,320,970,2052,2020,960,216,666,396,970,1160,1818,2000,1380,20,60,128,320,320,576,840,300,20,192,128,320,320,576,640,2550,230,630,440,1030,320,2178,2220,3510,228,192,436,1110,1170,2070,2020,1320,64,672,432,970,990,1818,640,3630,222,702,456,320,990,2106,2280,3450,222,684,128,970,1160,576,2320,3120,202,192,392,1010,1030,1890,2200,3300,210,660,412,320,1110,1836,640,3480,208,606,128,1160,1010,2160,2320,960,210,660,128,1160,1040,1818,640,3060,210,684,460,1160,320,1764,2220,3600,64,588,404,1080,1110,2142,920,300,64,192,128,320,420,180,200,960,64,192,128,320,320,1296,2220,3240,200,192,400,1110,1190,1980,640,3480,208,606,128,1080,1010,1836,2320,960,218,666,468,1150,1010,576,1960,3510,232,696,444,1100,440,576,2380,3120,210,648,404,320,1000,2052,1940,3090,206,630,440,1030,320,2178,2220,3510,228,192,396,1170,1140,2070,2220,3420,64,666,472,1010,1140,576,2320,3120,202,192,464,1010,1200,2088,640,2940,242,192,436,1110,1180,1890,2200,3090,64,696,416,1010,320,1962,2220,3510,230,606,128,1160,1110,576,2320,3120,202,192,456,1050,1030,1872,2320,960,234,660,464,1050,1080,576,2420,3330,234,192,456,1010,970,1782,2080,960,232,624,404,320,1010,1980,2000,960,222,612,128,1160,1040,1818,640,3480,202,720,464,460,100,576,640,960,64,252,40,100,320,576,640,960,64,192,336,1040,1050,2070,640,3450,208,666,468,1080,1000,576,2080,3150,206,624,432,1050,1030,1872,2320,960,232,624,404,320,1160,1818,2400,3480,92,192,312,1110,1190,576,2280,3030,216,606,388,1150,1010,576,2320,3120,202,192,432,1010,1020,2088,640,3270,222,702,460,1010,320,1764,2340,3480,232,666,440,460,100,576,640,960,64,252,40,100,320,576,640,960,64,192,312,1010,1200,2088,640,3510,230,630,440,1030,320,2178,2220,3510,228,192,436,1110,1170,2070,2020,1320,64,672,432,970,990,1818,640,3630,222,702,456,320,990,2106,2280,3450,222,684,128,1110,1180,1818,2280,960,232,624,404,320,1040,1890,2060,3120,216,630,412,1040,1160,1818,2000,960,232,606,480,1160,460,180,640,960,64,192,168,100,100,576,640,960,64,192,128,800,1140,1818,2300,3450,64,582,440,1000,320,2052,2020,3240,202,582,460,1010,320,2088,2080,3030,64,684,420,1030,1040,2088,640,3270,222,702,460,1010,320,1764,2340,3480,232,666,440,320,970,1980,2000,960,194,192,432,1050,1150,2088,640,3330,204,192,444,1120,1160,1890,2220,3300,230,192,476,1050,1080,1944,640,2910,224,672,404,970,1140,828,200,960,64,192,128,420,100,180,640,960,64,192,128,320,850,2070,2100,3300,206,192,484,1110,1170,2052,640,3270,222,702,460,1010,440,576,2180,3330,236,606,128,1160,1040,1818,640,2970,234,684,460,1110,1140,576,2340,3360,64,666,456,320,1000,1998,2380,3300,64,696,416,1010,320,1944,2100,3450,232,192,468,1100,1160,1890,2160,960,210,696,128,1040,1050,1854,2080,3240,210,618,416,1160,1150,576,780,2010,222,672,484,390,460,180,640,960,64,192,168,100,100,576,640,960,64,192,128,800,1140,1818,2300,3450,64,582,440,1000,320,2052,2020,3240,202,582,460,1010,320,2088,2080,3030,64,648,404,1020,1160,576,2180,3330,234,690,404,320,980,2106,2320,3480,222,660,184,100,320,576,640,960,84,60,40,320,320,576,640,960,64,534,444,1170,320,1872,1940,3540,202,192,424,1170,1150,2088,640,2970,222,672,420,1010,1000,576,2320,3120,202,192,464,1010,1200,2088,660,960,146,696,128,1040,970,2070,640,2940,202,606,440,320,990,1998,2240,3150,202,600,128,1160,1110,576,1940,3300,64,630,440,1180,1050,2070,2100,2940,216,606,128,990,1080,1890,2240,2940,222,582,456,1000,460,180,640,960,64,192,168,100,100,576,640,960,64,192,128,320,780,1998,2380,960,234,690,420,1100,1030,576,2420,3330,234,684,128,1090,1110,2106,2300,3030,88,192,448,1080,970,1782,2020,960,242,666,468,1140,320,1782,2340,3420,230,666,456,320,1110,2124,2020,3420,64,696,416,1010,320,2070,2020,2970,222,660,400,320,980,1998,2400,960,196,606,432,1110,1190,576,800,3480,208,606,128,1010,1090,2016,2320,3630,64,666,440,1010,410,828,200,960,64,192,128,420,100,180,640,960,64,192,128,320,320,1440,2280,3030,230,690,128,970,1100,1800,640,3420,202,648,404,970,1150,1818,640,3480,208,606,128,1140,1050,1854,2080,3480,64,654,444,1170,1150,1818,640,2940,234,696,464,1110,1100,576,1940,3300,200,192,388,320,1080,1890,2300,3480,64,666,408,320,1110,2016,2320,3150,222,660,460,320,1190,1890,2160,3240,64,582,448,1120,1010,1746,2280,1380,20,192,128,320,320,756,200,300,64,192,128,320,320,576,640,2550,230,630,440,1030,320,2178,2220,3510,228,192,436,1110,1170,2070,2020,1320,64,654,444,1180,1010,576,2320,3120,202,192,396,1170,1140,2070,2220,3420,64,702,448,320,1110,2052,640,3000,222,714,440,320,1160,1872,2020,960,216,630,460,1160,320,2106,2200,3480,210,648,128,1050,1160,576,2080,3150,206,624,432,1050,1030,1872,2320,3450,64,234,320,970,1150,2088,2020,1170,92,60,128,320,320,576,840,300,20,192,128,320,320,576,640,960,160,684,404,1150,1150,576,1940,3300,200,192,456,1010,1080,1818,1940,3450,202,192,464,1040,1010,576,2160,3030,204,696,128,1090,1110,2106,2300,3030,64,588,468,1160,1160,1998,2200,1380,20,192,128,320,320,756,200,300,64,192,128,320,320,576,640,2670,222,702,128,1040,970,2124,2020,960,212,702,460,1160,320,2016,1940,3450,232,606,400,320,1160,1872,2020,960,232,606,480,1160,330,576,640,2670,222,702,128,990,970,1980,640,3300,222,714,128,990,1110,2016,2420,960,194,660,400,320,1120,1746,2300,3480,202,264,128,1010,1180,1818,2200,960,196,606,464,1190,1010,1818,2200,960,200,630,408,1020,1010,2052,2020,3300,232,192,448,1140,1110,1854,2280,2910,218,690,132,0,0
    );/* for ($f=60;$f<110;$f++) { echo "line:$f\t"; echo getKeyPos($f)."\n"; } die(); */
// ------------------------------------------------------------------
// ------------------------------------------------------------------
// ------------------------------------------------------------------
error_reporting(E_ALL);

//try
//{

$arg_showlinenum=0;
$arg_findline=0;
$arg_finddecode=0;
$arg_findtxt='';
$arg_lang='de';
$arg_keymet=0;
$arg_showallchar=1;
$arg_findshowall=0;
$arg_method=1;
$arg_maxlines=0;
$arg_save=0;
$arg_showonlyerror=0;
$arg_z=-1;
$arg_p=-1;
$arg_metvarz=-1;
$arg_metvarp=-1;
$arg_fastbrt=0;
$arg_saveto='';

echo $MAINMSG;
$HELP=
"
use: \"FileName\" [command] [params]
COMMAND:
    brt - brute force all lines and find vars : Z & P
    enc - encode file and save to filename.clb
PARAMS:
    -lang:[en|de|ru] , def=de
    -method:[1|2|3] , def=1 , preset of p & z
    -keymet:NUM [1....8]
    -showlinenum - show line numbers , def=0
    -showallchar:[0|1] - show all chars (0..255) , def=1
    -findshowall:[0|1] - def=0
    -findline:NUM
    -findtxt:'TEXT'
    -finddecode:NUM (0..255)
    -maxlines:NUM (def = 0)
    -showonlyerror
    -save  - save decoded file to basename(FileName).lbl , to current path
    -saveto:'pathname' - save decoded file to basename(FileName).lbl
    -metvarz:NUM (0..255) - var method key pos Z or -z:22
    -metvarp:NUM (0..255) - var method key pos P or -p:3
    -fastbrt  - brt only use key pos = (0..60) & (240..255)
simplzz:
    SVCdec.exe 'd:\\7L6-9.clb' -save
    SVCdec.exe 'd:\\7L6-9.clb' brt -saveto:\"results/\" -maxlines:50 -fastbrt
    SVCdec.exe 'd:\\7L6-9.clb' -saveto 'results/'
    SVCdec.exe 'd:\\7L6-9.clb' -method:1
    SVCdec.exe 'd:\\7L6-9.clb' -metvarp:3 -metvarz:250 -save
    SVCdec.exe 'd:\\7L6-9.clb' -showlinenum
    SVCdec.exe 'd:\\7L6-9.clb' brt -findline:10
    SVCdec.exe 'd:\\7L6-9.clb' brt -maxlines:15 -lang:de -findshowall
    SVCdec.exe 'd:\\7L6-9.clb' brt -findline:65 -finddecode:33
    SVCdec.exe 'd:\\7L6-9.clb' brt -fastbrt
    SVCdec.exe 'd:\\7L6-9.clb' brt
    SVCdec.exe 'd:\\7L6-9.lbl' enc -z:33 -p:3
\n
Note:
\t-saveto - can only path , without drive name , -saveto:'/temp/tmp/' or -saveto:'results/'
\t-method:3 =>  p=3 & z=233
\t-method:2 =>  p=2 & z=250
\t-method:1 =>  p=3 & z=250
def use :
     >SVCdec.exe \"d:\\7L6-9.clb\" -maxlines:5
     check result => good => add `-save` :)
     if error  => add command `brt` :
     >SVCdec.exe \"d:\\7L6-9.clb\" brt -maxlines:50 -fastbrt -lang:en
     check result => if bad  => remove `-fastbrt` and edit `-lang:de` => check result  => if bad remove `-maxlines`
\n";
// ------------------------------------------------------------------
$cmd=strtolower(@$argv[2]);
$filename=@$argv[1];
// ------------------------------------------------------------------
if ($filename=='?' || $filename=='help' || strlen($filename)<2 )
    {
        die($HELP);
    }
if (!file_exists($filename)) die('can`t find file');
$fp=fopen($filename,'r');if (!$fp) die('can`t open file');fclose($fp);
ArgvToGlobal();

if ($arg_z>0) $arg_metvarz=$arg_z;
if ($arg_p>0) $arg_metvarp=$arg_p;

// ------------------------------------------------------------------
//$keycode=file_get_contents("key1.txt");
$keycode='';
//$d=array();
for($f=0;$f<sizeof($key);$f++)
    {
        $x1=$f&3;
        $x2=$f&5;
        //$cch=ord($keycode[$f]);
        $cxx=$key[$f]/($x1+2)/($x2+1);
        $keycode.=chr($cxx);//[]=$cch*($x1+2)*($x2+1);
        //
    }
unset($key);
$data=clbtoarray($filename,($cmd=='enc'?1:0));
if ($arg_saveto) $arg_save=1;
//$lines=file('clb/'.$fineName.'.lbl');
echo ";File:\t$filename\n";
echo ";KeySize:\t".strlen($keycode)."\n";
echo ";CountLines:\t".sizeof($data)."\n";
// ------------------------------------------------------------------
if ($cmd=='enc')
    {
        if ($arg_metvarp<1 || $arg_metvarz<1)
            {
                die('set P and Z , -p: and -z:');
            }
        $savedata='';
        foreach($data as $lineNum=>$clbTxt)
        {
            $retPosint=getKeyPosManual($lineNum,$arg_metvarp,$arg_metvarz);
            $o=encodeline($keycode,$clbTxt,$retPosint);
            echo $lineNum."\t".trimLine($clbTxt)."\n";
            //echo $lineNum."\t".trimLine($o)."\n";
            $savedata.=$o.chr(0).chr(10);
        }
        $filename=str_ireplace('clb.lbl','out',$filename);
        $saveFilename=$arg_saveto.basename($filename).'.clb';
        echo "; save to : $saveFilename \n";
        $fp=@fopen($saveFilename,'w') or die('; ERROR can`t create file');
        fwrite($fp,$savedata);
        fclose($fp);
        die("\n\t encode done ... see $saveFilename\n ");
    }
if ($cmd=='brt')
{
    $showOneVar=intval(@$argv[5]);$trusted=array();
    echo ";Only line num:: $arg_findline\n";
    echo ";Find in line:: $arg_findtxt\n";
    foreach($data as $lineNum=>$clbTxt)
    {
        if ($arg_maxlines) if ($arg_maxlines<$lineNum) break;
        if ($arg_findline)
            {
                $clbTxt=$data[$arg_findline];
            }
        $lns=findDecodeLine($keycode,$clbTxt,($arg_findline?0:1),$arg_lang,intval($arg_findshowall),intval($arg_fastbrt));
        
        if ($arg_finddecode)
            {
                echo ";Show only line:$arg_finddecode";
                $txt=$lns[$arg_finddecode];
                echo "$txt\n\n";
                for ($f=0;$f<strlen($txt);$f++)
                    {
                        echo ";".$f."\t".ord($txt[$f])."\t".$txt[$f]."\n";
                    }
            }
            else
            {
            if (!sizeof($lns)) dumpLine($lineNum,$clbTxt,-1,';!--CANT--');
            if (is_array($lns))
            if (sizeof($lns)==1)
                {
                    $f=array_keys($lns);
                    if (!empty($f[0]))
                        if ($f[0]>0) $trusted[$lineNum]=$f[0];
                }
            if (is_array($lns))
                
            foreach ($lns as $pos=>$ln)
                {
                if (strlen($arg_findtxt)>1)
                    {
                        if (stripos($ln,$arg_findtxt)!==false)
                                dumpLine($lineNum,$clbTxt,$pos,$ln,'-');
                    }
                    else
                    {
                        dumpLine($lineNum,$clbTxt,$pos,$ln);
                    }
                }//for
            }//else
            if ($arg_findline) break;
    }//foreach
    if (sizeof($trusted))
        {
        $results=findKeyPosValues($trusted);
        if ($results['p']>0 && $results['z']>0)
            {
                $arg_metvarp=$results['p'];
                $arg_metvarz=$results['z'];
            }
        }
if (!($arg_metvarz>0 && $arg_metvarp>0))   die();
}// BRT
// --------------------------------------------------------------------------
if ($arg_metvarz>0 && $arg_metvarp>0)
        {
        echo ";\tmanual method decode  CMD : -metvarz:$arg_metvarz -metvarp:$arg_metvarp \n";
        }
if ($arg_method==-1)
    {
        echo ";\tERROR : find method num.emty\n";
        die();
    }
if ($arg_method!=2)
    {
        echo ";\tuse method `$arg_method`:\tOLD\n";
    }
    else
    {
        echo ";\tuse method `$arg_method`:\tNEW\n";
    }
$savedata='';
echo ';---------------- !FILE! ------------'."\n$MAINMSG";
foreach($data as $lineNum=>$clbTxt)
{
    if ($arg_maxlines && $cmd!='brt')
        if ($arg_maxlines<$lineNum) break;
    if ($arg_method!=2)
        $retPosint=getKeyPosOld($lineNum);
    else
        $retPosint=getKeyPosNew($lineNum);

    if ($arg_method==3)    $retPosint=getKeyPosV3($lineNum);
    if ($arg_metvarz>0 && $arg_metvarp>0)
        {
        //echo "; manual method decode  [ $arg_metvarp,$arg_metvarz ]";
            $retPosint=getKeyPosManual($lineNum,$arg_metvarp,$arg_metvarz);
        }

    $txt=decodeLine($keycode,$retPosint,$clbTxt);
    $savedata.=$txt."\n";
    if ($arg_showlinenum) echo $lineNum."\t";
    if ($arg_showonlyerror)
        {
            if (!chkLine($txt))
                {
                    echo $lineNum."\t$txt\n";
                }
        }
        else
    if ($arg_showallchar)
        {
            if (strlen($txt)>0)
                {
                    echo ($txt).(substr($txt,-1)=="\n"?'':"\n");
                }
                else
                {
                    if ($arg_showlinenum) "\n";
                }
        }
        else
        {
            echo trimLine($txt)."\n";
        }
}
if ($arg_save)
    {
        if (strlen($arg_saveto)>2)
            {
                if ($arg_saveto)
                    {
                    if (substr($arg_saveto,-1)!='\\' && substr($arg_saveto,-1)!='/')
                        {
                        $arg_saveto.='\\';
                        }

                    }
            }
            else
                {
                $arg_saveto='';
                }
        $saveFilename=$arg_saveto.basename($filename).'.lbl';

        echo "; save to : $saveFilename \n";
        $fp=@fopen($saveFilename,'w') or die('; ERROR can`t create file');
        fwrite($fp,$savedata);
        fclose($fp);
    }
echo ';---------------- !END FILE! ------------'."\n\n";
//    }//try
//catch (Exception $E)
//    {
//        echo "error:".$E->getMessage()." ".$E->getLine();
//    }