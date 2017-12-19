<?php
/*
 *	Made by SirHyperNova
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  File Manager Module
 */
?>
    </main>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.modal').modal({
                dismissible: false
            });
            $('#new-file-type').change(function () {
                $('#new-file-content-div').toggle(250);
            });
            $('#edit-file-type').change(function () {
                $('#edit-file-content-div').toggle(250);
            });
            var imgdata = null;
            var pdfdata = null;
            <?php if (isset($_GET['medit']) && isset($edit) && is_a($edit,'File') || is_a($edit,'Folder')) { ?>
                    $('#edit-modal').modal('open');
                    $('#edit-file-content').trigger('autoresize');
            <?php }
            if (isset($_GET['image'])) {
                $ifile = $afiles->get($_GET['image']);
                if ($file->ext == 'png' || $file->ext == 'jpg' || $file->ext == 'jpeg') {
                    if (is_a($ifile,'File')) { ?>
                          imgdata = '<?php echo base64_encode($ifile->data()); ?>';
                    <?php }
                }
            } ?>
            if (imgdata != null) {
                    var image = window.open('','_blank',"toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=4000,height=4000");
                    image.document.write('<html><head><title>Image Preview</title><style>* {margin:0;border:none;} body{background-color:grey;} img {position:relative;left:50%; top:50%; transform: translateX(-50%) translateY(-50%);} button {position:fixed; right:0;}</style></head><body>');
                    image.document.write('<img src="data:image/png;base64,'+imgdata+'"><button id="button">Close</button>');
                    image.document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"><\/script><script>$("#button").click(function () {window.close()})<\/script>');
                    image.document.write('</body></html>');
            }
            <?php if (isset($_GET['pdf'])) {
                $pfile = $afiles->get($_GET['pdf']);
                if (is_a($pfile,'File')) { ?>
                      pdfdata = '<?php echo base64_encode($pfile->data()); ?>';
                <?php }
            } ?>
            if (pdfdata != null) {
                    var pdf = window.open('','_blank',"toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=4000,height=4000");
                    pdf.document.write('<html><head><title>PDF Preview</title><style>* {margin:0;border:none;} body{background-color:grey;} iframe {position:relative;left:50%; top:50%; width: 100%; height: 100%; transform: translateX(-50%) translateY(-50%);} button {position:fixed; right:0;}</style></head><body>');
                    pdf.document.write('<iframe src="data:application/pdf;base64,'+pdfdata+'"></iframe><button id="button">Close</button>');
                    pdf.document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"><\/script><script>$("#button").click(function () {window.close()})<\/script>');
                    pdf.document.write('</body></html>');
            }
        });
    </script>
    </body>
</html>
