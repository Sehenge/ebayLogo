/**
 * Created with JetBrains PhpStorm.
 * User: Alex
 * Date: 5/9/13
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
$(".dir").click(function(){
    var brand = $(this).text();
    var container = $('.container');
    container.empty();
    console.log('click!');
    $.ajax({
        type: "POST",
        url: "scanner.php",
        data: {data: brand}
    }).done(function( msg ) {
            var arr = JSON.parse(msg);
            for (var i = 0; i < arr.length; i++) {
                container.append('<div><img src="' + brand + '/' + arr[i] + '"/><span>' + arr[i] + '</span></div>');
                console.log(arr.length + ' i: ' + i);
            }
        });
})
