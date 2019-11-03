<?php
function Init_ICONS() {//********************************************************
    global 	$ICONS;

    //*********************************************************************
    function icon_txt($border='#333', $lines='#000', $fill='#FFF', $extra1="", $extra2=""){
        return '<svg version="1.1" width="14" height="16">'.
            '<rect x = "0" y = "0" width = "14" height = "16" fill="'.$fill.'" stroke="'.$border.'" stroke-width="2" />'.$extra2.
            '<line x1="3" y1="3.5"  x2="11" y2="3.5"  stroke="'.$lines.'" stroke-width=".6"/>'.
            '<line x1="3" y1="6.5"  x2="11" y2="6.5"  stroke="'.$lines.'" stroke-width=".6"/>'.
            '<line x1="3" y1="9.5"  x2="11" y2="9.5"  stroke="'.$lines.'" stroke-width=".6"/>'.
            '<line x1="3" y1="12.5" x2="11" y2="12.5" stroke="'.$lines.'" stroke-width=".6"/>'.$extra1.'</svg>';
    }//end icon_txt() //***************************************************


    function icon_folder($extra = "") {//**********************************
        return '<svg version="1.1" width="18" height="14"><g transform="translate(0,0)">'.
            '<path  d="M0.5, 1  L8,1  L9,2  L9,3  L16.5,3  L17,3.5  L17,13.5  L.5,13.5  L.5,.5" '.
            'fill="#F0CD28" stroke="rgb(200,170,15)" stroke-width="1" />'.
            '<path  d="M1.5, 8  L7, 8  L8.5,6.3  L16,6.3  L7.5, 6.3   L6.5,7.5  L1.5,7.5" '.
            'fill="transparent" stroke="white" stroke-width="1" />'.
            '<path  d="M1.5,13  L1.5,2  L7.5,2  L8.5,3  L8.5,4  L15.5,4 L16,4.5  L16,13" '.
            'fill="transparent" stroke="white" stroke-width="1" />'.
            $extra.'</g></svg>';
    }//end icon_folder() //************************************************


    //Some common components
    $circle_x = '<circle cx="5" cy="5" r="5" stroke="#D00" stroke-width="1.3" fill="#D00"/>'.
        '<line x1="2.5" y1="2.5" x2="7.5" y2="7.5" stroke="white" stroke-width="1.5"/>'.
        '<line x1="7.5" y1="2.5" x2="2.5" y2="7.5" stroke="white" stroke-width="1.5"/>';

    $circle_plus = '<circle cx="5" cy="5" r="5" stroke="#080" stroke-width="0" fill="#080"/>'.
        '<line x1="2" y1="5" x2="8" y2="5" stroke="white" stroke-width="1.5" />'.
        '<line x1="5" y1="2" x2="5" y2="8" stroke="white" stroke-width="1.5" />';

    $circle_plus_rev = '<circle cx="5" cy="5" r="5" stroke="#080" stroke-width="1.3" fill="white"/>'.
        '<line x1="2" y1="5" x2="8" y2="5" stroke="#080" stroke-width="1.5" />'.
        '<line x1="5" y1="2" x2="5" y2="8" stroke="#080" stroke-width="1.5" />';

    $pencil = '<polygon points="2,0 9,7 7,9 0,2" stroke-width="1" stroke="darkgoldenrod" fill="rgb(246,222,100)"/>'.
        '<path  d="M0,2    L0,0  L2,0"   stroke="tan"    stroke-width="1" fill="tan"/>'.
        '<path  d="M0,1.5  L0,0  L1.5,0" stroke="black"  stroke-width="1.5" fill="transparent"/>'.
        '<line x1="7.3" y1="10"  x2="10" y2="7.3" stroke="silver" stroke-width="1"/>'.
        '<line x1="8.1" y1="10.8"  x2="10.8" y2="8.1"  stroke="red" stroke-width="1"/>';

    $img_0 = '<rect x="0"    y="0"   width="14" height="16" fill="#FF8" stroke="#44F" stroke-width="2"/>'.
        '<rect x="2"    y="2"   width="5"  height="5"  fill="#F66" stroke-width="0" />'.
        '<rect x="7.5"  y="6"   width="5"  height="5"  fill="#6F6" stroke-width="0" />'.
        '<rect x="2"    y="10"  width="5"  height="5"  fill="#66F" stroke-width="0" />';

    $arc_arrow = '<path d="M 3.5,12 a 30,30 0 0,1  9,-9  l -1.5,-2.4  l 6,1.3  l -1.6,6 l -1.5,-2.4'.
        ' a 30,30 0 0,0 -9,6.5 Z"  fill="white" stroke="blue" stroke-width="1.1" />';

    $up_arrow = '<polygon points="6,0  12,6  8,6  8,11  4,11  4,6  0,6" stroke-width="1" stroke="white" fill="green" />';

    $zero = '<rect x="0"  y="0"  width="3" height="6" fill="transparent" stroke="#555" stroke-width="1" />';
    $one  = '<line x1="0" y1="-.5"   x2="0" y2="6.5"  stroke="#555" stroke-width="1"/>';

    $extra_up  = '<g transform="scale(1.1) translate(1.75,4)">'.$up_arrow.'</g>';
    $extra_new = '<g transform="translate(4,6)">'.$circle_plus.'</g>';
    $extra_z   = '<text x="4" y="12" style="font-size:8pt;font-weight:900;fill:blue ;font-family:Arial;">z</text>';

    //The icons
    $ICONS['bin'] =  '<svg version="1.1" width="14" height="16">'.
        '<g transform="translate( 0.5,0.5)">'.$one .'</g>'.
        '<g transform="translate( 3.5,0.5)">'.$zero.'</g>'.'<g transform="translate( 9.5,0.5)">'.$one .'</g>'.
        '<g transform="translate(12.5,0.5)">'.$one .'</g>'.'<g transform="translate( 0.5,9.5)">'.$zero.'</g>'.
        '<g transform="translate( 6.5,9.5)">'.$one .'</g>'.'<g transform="translate( 9.5,9.5)">'.$zero.'</g>'.
        '</svg>';
    $ICONS['z'] = icon_txt('#333','#FFF','#FFF',$extra_z);
    $ICONS['img'] = '<svg version="1.1" width="14" height="16">'.$img_0.'</svg>';
    $ICONS['svg'] = icon_txt('#333', '#444', '#FFF', "", $img_0);
    $ICONS['txt'] = icon_txt('#333', '#000', '#FFF');
    $ICONS['htm'] = icon_txt('#444', '#222', '#FABEAA'); //rgb(250,190,170)
    $ICONS['php'] = icon_txt('#333', '#111', '#C3C3FF'); //rgb(195,195,225)
    $ICONS['css'] = icon_txt('#333', '#111', '#FFE1A5'); //rgb(255,225,165)
    $ICONS['cfg'] = icon_txt('#444', '#111', '#DDD');
    $ICONS['dir']    = icon_folder();
    $ICONS['folder'] = icon_folder();
    $ICONS['folder_new'] = icon_folder('<g transform="translate(7.5,4)">'.$circle_plus.'</g>');
    $ICONS['upload']     = icon_txt('#333', 'black', 'white', $extra_up);
    $ICONS['file_new']   = icon_txt('#444', 'black', 'white', $extra_new);
    $ICONS['ren_mov'] = icon_folder('<g transform="translate(2.5,3)">'.$pencil.'</g>'.$arc_arrow);
    $ICONS['move']    = icon_folder($arc_arrow);
    $ICONS['copy']    = '<svg version="1.1" width="12" height="14"><g transform="translate(1,2)">'.$circle_plus_rev.'</g></svg>';
    $ICONS['delete']  = '<svg version="1.1" width="12" height="14"><g transform="translate(1,2)">'.$circle_x.'</g></svg>';
    $ICONS['up_dir']  = icon_folder('<g transform="scale(1.1) translate(1.75,2) rotate(-45, 5, 5)">'.$up_arrow.'</g>');

    if (!supports_svg()) { //Text "icons" if SVG not supported.  Mostly for IE < 9
        foreach (array_keys($ICONS) as $key) {
            $ICONS[$key] = "";
        }
        $ICONS['up_dir']  = '[&lt;]';
        $ICONS['dir']	  = '[+]';
        $ICONS['folder']  = '[+]';
        $ICONS['ren_mov'] = '<span class="RCD1 R">&gt;</span>';
        $ICONS['move']    = '<span class="RCD1 R">&gt;</span>';
        $ICONS['copy']    = '<span class="RCD1 C">+</span>';
        $ICONS['delete']  = '<span class="RCD1 D">x</span>';
    }
}//end Init_ICONS() {//*********************************************************

?>
