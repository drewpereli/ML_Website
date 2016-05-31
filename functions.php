<?php
function replaceUnderscores($string)
{
    return str_replace('_', ' ', $string);
}

function replaceSpaces($string)
{
    return str_replace(' ', '_', $string);
}


function indexOf($array, $item)//Returns the index of "item" in the numerical array "array". Returns -1 if it's not present
{
    $i = 0;
    foreach($array as $arrayItem)
    {
        if ($arrayItem == $item)
        {
            return $i;
        }
        $i++;
    }
    
    return -1;
}

?>

<html>
    <script>
        function replaceUnderscores(string)
        {
            return string.replace('_', ' ');
        }
        
        function replaceSpaces(string)
        {
            return string.replace(' ', '_');
        }
    </script>
</html>