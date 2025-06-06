<?php

/**
 * Created by Yogi Pratama Putra
 * Copyright 2014 | mail : youputra@gmail.com
 */

class InputForm
{
    function inputText($type,$name,$class,$value="",$maxlenght="",$placeholder="",$helper="",$helper_class="",$additional=""){
        echo "<input type='".$type."' name='".$name."' id='".$name."' class='".$class."' maxlength='".$maxlenght."' value='".$value."' placeholder='".$placeholder."' ".$additional."/>";
        if($helper!="")
            echo "<span class='".$helper_class."'>".$helper."</span>";
    }
    
    function inputHidden($name,$value=""){
        echo "<input type='hidden' name='".$name."' id='".$name."' value='".$value."'/>";
    }
    
    function inputRadio($name=array(),$class,$label="",$selected=""){
        echo "<input type='text' name='' id='' class='' maxlength=''/>";
    }
    
    function inputSelect($name,$data=array(),$value=array(),$class,$selected="",$first_option="",$additional=""){
        echo "<select name='".$name."' id='".$name."' class='".$class."' ".$additional.">";
        if($first_option!="") echo "<option value='0'>".$first_option."</option>";
        foreach($data as $key=>$val){
            if($value[$key]==$selected)
                $sselected="selected=''";
            else
                $sselected="";
            echo "<option value='".$value[$key]."' ".$sselected.">".$val."</option>";
        }
        echo "</select>";
    }
    
    function inputTextArea($name,$class,$value="",$row="",$col="",$placeholder="",$helper="",$helper_class=""){
        echo "<textarea name='".$name."' id='".$name."' class='".$class."' rows='".$row."' cols='".$col."' placeholder='".$placeholder."'>".$value."</textarea>";
        if($helper!="")
            echo "<span class='".$helper_class."'>".$helper."</span>";
    }
}
?>