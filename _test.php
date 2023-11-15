<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
<script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
<script type="text/javascript" src="include/ajaxupload.3.5.js" ></script>
<script type="text/javascript" src="include/jquery.contextMenu.min.js"></script>
<link rel="stylesheet" type="text/css" href="include/jquery.contextMenu.min.css">
        <script type='text/javascript'>

$(document).ready(function(){
    $(':checkbox').on('change', function()
            {
		var perv = "2_sort_izdelie";
		var secon = "1";
                var sendData = $(this).closest('form').serialize();
                $.ajax({
                    url: 'save.php',
                    type: 'POST',
                    data: {
				id:perv,
				content:secon
			},
                    success: function(data)
                    {
                        alert(data); // заменить на нужное
                    }
                });
            });
});

        
        </script>
    </head>
    <body>
        <form>
            <input name="one" value="1" type="checkbox"/>
            <input name="two" value="2" type="checkbox"/>
        </form>
    </body>
</html>