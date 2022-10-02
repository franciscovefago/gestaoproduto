$(document).ready(function () {
    $('#msbo').on('click', function () {
        $('body').toggleClass('msb-x');
    });

    $('.dropdown-submenu a.test').on("click", function (e) {
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });

    //SIDEBAR
    $('[data-bs-toggle="offcanvas"]').click(function () {
        $('#wrapper').toggleClass('toggled');
    });

    $(".deleteConfirm").click(function (e) {
       
      
        if (!confirm('Deseja Realmente excluir este registro?')) {
            return false;
        }
    });
});