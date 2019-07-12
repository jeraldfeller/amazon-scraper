<?php include('header.php');?>
      <div class="container container-fluid">
        <div class="row" style="margin-top: 25px;">
            <div class="col-md-12 text-center">
              <h4>Import CSV File</h4> <a href="proxies.php">edit proxies</a>
            </div>
            <div class="col-md-12" style="margin-top: 25px;"></div>
            <form id="importFile" class="form-inline col-md-12">
              <div class="col-md-6">
                <div class="input-group mb-3">

                            <input type="file" name="importFile" id="import-file-holder">
                            <input type="hidden" value="for review" name="action" id="forStatus">

                </div>
              </div>
              <div class="col-md-6" style="margin-top: -15px;">
                <select class="custom-select" name="importType" style="width: 100%;">
                  <option value="0">-- data type --</option>
                  <option value="1">Asin/Locale</option>
                  <option value="2">Link</option>
                </select>
              </div>
              <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> IMPORT</button>
              </div>
            </form>
        </div>
      </div>

      <script>
            $('#import-file-holder').on('change', function(){
                $fileName = $(this).val().split('\\');
                $('#upload-file-info').html($fileName.pop());
            });
            $("form#importFile").submit(function(event){

            //disable the default form submission
            event.preventDefault();
            event.stopPropagation();

            var formData = new FormData($(this)[0]);
            $.ajax({
                url: '/am/Controller/scraper.php?action=import',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response){
                  console.log(response);
                  if(response != 1){
                    alert('Please complete the input fields.');
                  }else{
                    alert('Import Successfull!');
                  }
                },
                error: function(xhr, textStatus, errorThrown){

                }
            });

            return false;

        });
      </script>
<?php include('footer.php');
