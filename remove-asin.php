<!DOCTYPE html>
<html>
  <head>
    <title>AM Scraper</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  </head>
  <body>
      <div class="container container-fluid">
        <div class="row" style="margin-top: 25px;">
            <div class="col-md-12 text-center">
              <h4>Remove Asin (upload csv file)</h4>
            </div>
            <div class="col-md-12" style="margin-top: 25px;"></div>
            <form id="importFile" class="form-inline col-md-12">
              <div class="col-md-6">
                <div class="input-group mb-3">

                            <input type="file" name="importFile" id="import-file-holder">
                            <input type="hidden" value="for review" name="action" id="forStatus">

                </div>
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
                url: '/Controller/scraper.php?action=remove',
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
  </body>
</html>

