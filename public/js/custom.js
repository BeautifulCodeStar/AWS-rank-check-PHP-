const url = '/router/Router.php'
$('.submit').click(e => {
    e.preventDefault()
    const search = $('.search').val()
    if (!search) {
        $('.error').css('display', 'block')
        return true
    } else {
        $('.error').css('display', 'none')
        $('.submit').attr('disabled', true)
        console.log("Submit", search)
        $.ajax({
            url: url,
            type: 'GET',
            data: { product: search },
            success: res => {
                $('.submit').attr('disabled', false)
                if (res) {
                    const parse = JSON.parse(res)
                    const result = JSON.stringify(parse)
                    console.log("parse result", parse)
                } else {
                    $('.result').html('Cannot found any products related to that')
                }
            }
        })
    }
})

$('button.add-btn').click(e => {
    $('.add-rank-tracker').css({ 'display': 'block', 'height': 'auto'})
})
$('button.save-btn').click(e => {
    
})

$('.save-btn').click(e => {
    const asin = $('.add-asin').val()
    const phrases = $('.textarea').val()
    if (asin && phrases) {
        $.ajax({
            url: url,
            data: {
                asin: asin,
                phrases: phrases
            },
            type: "POST",
            success: res => {
                $('.add-rank-tracker').css({ 'display': 'none', 'height': '0px'})
                console.log("Add asin and phrases response___", res)
            }
        })
    } else {
        $('.error1').css('display', 'block')
        console.log('Provide Asin and Phrases')
    }
})