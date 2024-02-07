$(".fa-copy").on('click', function(){
    let el = $(this).closest("td")[0].outerText;
    navigator.clipboard.writeText(el);
    $(this).attr("title", "Copied");
    setTimeout(() => {
        $(this).attr("title", "Copy");
    }, 3000)
})
