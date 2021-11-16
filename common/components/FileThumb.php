<?php
namespace common\components;

use yii\base\Component;

class FileThumb extends Component
{
    public $config = [
        'defaultIcon' => 'img/filethumbs/default.png',
        'jpgIcon' => 'img/filethumbs/jpgIcon.png',
        'pngIcon' => 'img/filethumbs/pngIcon.png',
        'gifIcon' => 'img/filethumbs/gifIcon.png',
        'pdfIcon' => 'img/filethumbs/pdfIcon.png',
        'docIcon' => 'img/filethumbs/docIcon.png',
        'docxIcon' => 'img/filethumbs/docxIcon.png',
        'xlsIcon' => 'img/filethumbs/xlsIcon.png',
        'xlsxIcon' => 'img/filethumbs/xlsxIcon.png',
        'iconWidth' => '80px',
        'iconHeight' => '80px',
        'target' => '_new'
    ];

    public function init() {

        // Init this component
        // $this->someconfig = ...

echo <<<EX
<script type="text/javascript">
window.onload = function() {
$('.file-thumb img').click(function(data) {    
window.open($(this).parent().attr('filename'));
})
$('.file-thumb .del-btn').click(function(data) {

    Swal.fire({
        title: 'Atenção!',
        text: "Tem certeza que deseja excluir o arquivo?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
    }).then((result) => {
        if (result.value) {
            thumb = $(this).parent();
            url = thumb.attr('delete-url');
            console.log(url)
            idFile = thumb.attr('id-file');
            params = {'id': idFile}
            $.post(url, params, function(result){
                console.log(result)
                console.log($(this).parent())
                thumb.remove();
                Swal.fire('Excluído!', 'Arquivo foi excluído.', 'success')
            });
        }
    })
})
}
</script>
<style>
.file-thumb {
position: relative;
width: {$this->config['iconWidth']};
height: {$this->config['iconHeight']};
margin: 5px;
display: inline-block;
cursor: pointer;
}
.file-thumb .del-btn {
position: absolute;
top: -5px;
right: -5px;
padding: 2px 6px;
border-radius: 50px;
font-weight: 800;
font-size: 10px;
cursor: pointer;
}
</style>
EX;

    }

    public function display ($file, $type, $deleteUrl, $classes='') {
        // $element = '<a href="' . $file . '" target="_new" class="file-thumb ' . $classes . '" delete-url="' . $deleteUrl . '">';
        $element = '<div filename="' . $file . '" class="file-thumb ' . $classes . '" delete-url="' . $deleteUrl . '" style="border: 0.5px solid #000;">';
        //        $element .= '<img class="img-responsive" src="' . $this->config[ $type. 'Icon' ] . '">';
        if($type=='pdf'){
            $getFile = $this->config[ $type. 'Icon' ];
        } else {
            $getFile = $file;
        }
        $element .= '<img class="img-responsive" src="'.$getFile.'">';
        $element .= '<div class="del-btn bg-red">X</div>';
        $element .= '</div>';

        echo $element;
    }
}

