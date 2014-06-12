<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/rateit.css" />
<script type='text/javascript' src="jquery-1.10.2.min.js"></script>
<script type='text/javascript' src="jquery.rateit.min.js"></script>

</head>

<body>
<div class="rateit" id="rateitbook" data-rateit-step="1">
</div>

<script type="text/javascript">
       var tooltips = ['Did not like it', 'It was OK', 'I liked it', 'REALLY liked it', 'I loved it'];
        $("#rateitbook").bind('over', 
            function (event, value) { 
                $(this).attr('title', tooltips[value - 1]); 
            }); 
        $("#rateitbook").bind('rated', 
            function() { 
                alert('rating: ' + $(this).rateit('value')); 
            });
</script>


</body>
</html>