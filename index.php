<?php
    session_start();
    
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.css">
    
    <style>
        .constainer-fluid { position: relative; width: 100%; height: 100%; }
        .product-search { width: 40%; margin: 0; }
        .product-title-array {
            background: white;
            width: 95%;
            height: 100% auto;
            word-break: break-all;
            padding: 20px;
            margin: 0 auto;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
        }
        .seach { position: relative; width: 100%; padding: 10px; }
        .error, .error1 { display: none; color: red; font-family: monospace; }
        .error1 { margin-left:16px; }
        .form-group>label { display: block; }
        .form-group {position:relative; display: inline-block; }
        textarea { width: 100%; display: block; padding: 10px; }
        textarea::-webkit-input-placeholder { color: rgba(169, 160, 155, 0.5);}
        .save-asin-keywords, .add-asin-keywords { margin-top: 10px;}
        .header { border: 1px solid #eeeeee;}
        .add-rank-tracker { position:relative; height: 0px; display: none; }
        .header>* { display:inline;float:left;}
        .product, .ranking-index, .ranking-avg, .keywords, .top-ranking {
            width: 10%;
            text-align: center;
            margin: 0px 1% 0 1%;
            text-align: center;
            align-self: center;
        }
        .present-product { width: 100%; height: 30px;auto;}
        .inc-dec-btns>i { position:relative;display: block; height: 6px;}
        .inc-dec-btns {display: inline; width:10px; float: right; line-height: 14px;}
        @media only screen and (max-width: 700px) { 
            .product-title-array { width: 95%;}
            .header > * { width: 100%; text-align:left;}
            .product-search {width: 100%;}
        }
        @media only screen and (max-width: 1200px) {
            .header { font-size : 11px; }
        }
    </style>
</head>

    <body>
        <div class="container-fluid product-title-array">
            <div class='header computer only row'>
                <div class="product">
                    <span class="info">Product</span>
                    <div class="inc-dec-btns">
                        <i class="angle up icon"></i>
                        <i class="angle down icon"></i>
                    </div>
                </div>
                <form class="product-search container" >
                    <div class="ui large icon input">
                        <input type="text" class="search" name="search" placeholder="title or ASIN" required>
                        <i class="search icon"></i>
                    </div>
                    <button type="submit" class="submit ui inverted blue button">Search</button>
                    <div class="error">Please input the product name to search</div>
                </form>
                <div class="ranking-index">
                    <span class="info">Ranking Index</span>
                    <div class="inc-dec-btns">
                        <i class="angle up icon"></i>
                        <i class="angle down icon"></i>
                    </div>
                </div>
                <div class="keywords">
                    <span class="info">Keywords</span>
                    <div class="inc-dec-btns">
                        <i class="angle up icon"></i>
                        <i class="angle down icon"></i>
                    </div>
                </div>
                <div class="ranking-avg">
                    <span class="info">Ranking(avg)</span>
                    <div class="inc-dec-btns">
                        <i class="angle up icon"></i>
                        <i class="angle down icon"></i>
                    </div>
                </div>
                <div class="top-ranking">
                    <span class="info">Top Ranking</span>
                    <div class="inc-dec-btns">
                        <i class="angle up icon"></i>
                        <i class="angle down icon"></i>
                    </div>
                </div>
            </div>
            <div class="present-product">
                <!-- <image src="https://images-na.ssl-images-amazon.com/images/51xXsRqcHZL.jpg"> -->
            </div>
            <div align="right" class="add-asin-keywords">
                <button type="button" class="ui inverted blue button add-btn">Add</button>
            </div>
            <div class="add-rank-tracker computer only row">
                <h4 class="col-lg-12 col-md-12 col-sm-12"> Add A New Product Rank Tracker </h4>

                <div class="form-group col-lg-1 col-md-4 col-sm-12">
                    <label for="asin">ASIN</label>
                    <div class="ui input">
                        <input type="text" class="add-asin" placeholder="Please input ASIN to add">
                    </div>
                </div>

                <div class="form group col-lg-7 col-md-7 col-sm-12">
                    <label for="keywords">Keywords</label>
                    <textarea class="textarea" rows="10" cols="70" placeholder="Please input keywords to add"></textarea>  
                </div>
                <div class="error1">Please input Asin and Phrases</div> 
                <div align="right" class="save-asin-keywords  col-lg-12 col-md-12 col-sm-12">
                    <button type="button" class="ui inverted blue button save-btn">Save</button>
                </div>
            </div>
            
        </div>
        
   
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.js"></script>
        <script src="public/js/custom.js"></script>
    </body>
</html>