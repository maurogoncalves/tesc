<?php
namespace common\components;

use yii\base\Component;

class FileTable extends Component
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
$('.file-thumb').click(function(data) {
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
            url = $(this).attr('delete-url');
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

.xclose {
    color:red;
}
</style>
EX;

    }

    public function display ($documento, $deleteUrl, $classes='') {
        $element = '<tr>';
            $element .= '<td>';
            $element .= '<a href="'.$documento->arquivo.'" target="_blank">'.$documento->nome.'</a>';
            $element .= '</td>';
            $element .= '<td align="center" filename="' . $deleteUrl . '" class="file-thumb ' . $classes . '" delete-url="' . $deleteUrl . '" >';
                // $element .= '<div >';
                // $element .= '<a href="' . $deleteUrl . '" target="_new" class="file-thumb ' . $classes . '" delete-url="' . $deleteUrl . '">';
                $element .= '<i class="fas fa-times xclose"></i>';
                // $element .= '</a>';
            $element .= '</td>';
        $element .= '</tr>';
        return $element;
    }
}

