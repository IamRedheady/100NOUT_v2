<?php /* Template Name: Сравнение */ ?>
<?php get_header();?>

<!-- Compare Styles -->
<style>
    .compare__top .dropdown {
        display: none;
    }
    .br_new_compare .br_right_table p.product__price {
        overflow: initial !important;
        line-height: 36px !important;
    }
    .br_new_compare .br_right_table p.product__price-old {
        margin-left: auto !important;
    }
    .br_new_compare_block_wrap {
        margin-top: 42px;
    }
    .br_opacity_top {
        display: none;
    }
    .br_main_top-1 {
        width: 295px;
    }
    .br_main_top-2 {
        display: flex;
    }
    /* .br_top_table {
        display: flex;
    } */
    .br_new_compare_block .br_top_table .br_remove_all_compare, .br_new_compare_block .br_top_table .br_show_compare_dif {
        position: relative;
        z-index: 1;
    }
    .br_new_compare .br_right_table thead tr th, .br_new_compare .br_left_table .br_header_row {
        block-size: 0px !important;
    }
    .br_top_table .br_main_top {
        position: static;
    }
    .br_new_compare_block table, .br_new_compare_block th, .br_new_compare_block td {
        border: none !important;
    }
    .br_new_compare .br_right_table th, .br_new_compare .br_right_table td, .br_top_table th, .br_top_table td {
        text-align: left !important;
    }
    td .product:hover {
        box-shadow: none;
        border-color: transparent;
    }
    td .product__buy {
        margin-bottom: 0;
        margin-left: auto;
    }
    td .product {
        max-width: 300px;
    }
    td .product__swiper-wrap {
        max-width: 100%;
    }
    td div.product {
        padding: 0px !important;
    }
    .br_new_compare .br_left_table th {
        text-align: left !important;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        color: #5C5C5C;
    }
    .br_right_table thead {
        display: none;
    }
    .compare * {
        user-select: none;
    }
    .compare .br_right_table {
        cursor: grab;
    }
    .compare .br_right_table:active {
        cursor: grabbing;
    }
    .br_new_compare_block .br_remove_compare_product_reload {
        display: block;
        position: static;
    }
    .br_top_table {
        display: flex;
    }
    .br_top_table-wrap {
        width: 295px;
        min-width: 295px;
    }
    div.br_top_table div.br_main_top {
        margin-left: 0 !important;
        z-index: 10 !important;
    }
    .br_new_compare_block .br_top_table .br_remove_all_compare, .br_new_compare_block .br_top_table .br_show_compare_dif {
        float: initial;
        display: block;
    }
    .br_top_table-inputs label {
        display: flex;
        align-items: center;
        color: #474747;
        cursor: pointer;
        margin-bottom: 8px;
    }
    .br_top_table-inputs label input {
        display: none;
    }
    .br_top_table-inputs label span {
        display: block;
        border: 1px solid #A3A3A3;
        border-radius: 16px;
        width: 20px;
        height: 20px;
        min-width: 20px;
        margin-right: 8px;
    }
    .br_top_table-inputs label input:checked + span {
        border: 5.5px solid #69C856;
    }
    .br_show_compare_dif {
        display: none !important;
    }
    .compare__top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .br_remove_all_compare {
        display: none !important;
    }
    div.br_new_compare .br_right_table tr td, div.br_new_compare .br_right_table tr th, div.br_new_compare .br_left_table, div.br_new_compare_block .br_top_table table th, div.br_new_compare_block .br_top_table table td {
        min-width: 290px !important;
    }
    div.br_new_compare div.br_right_table {
        margin-left: 295px !important;
    }
    .btn.btn-remo {
        padding: 10px 14px !important;
        display: flex !important;
        position: absolute;
        right: 10px;
        top: 7px;
        z-index: 2;
        height: 40px;
        width: 40px;
        background: none;
    }

    @media screen and (max-width: 768px) {
        .br_new_compare .br_right_table table tbody th {
            display: none !important;
        }
        .br_new_compare .br_left_table {
            display: block !important;
            background-color: transparent !important;
        }
        .br_new_compare_block .br_same_attr {
            background-color: transparent !important;
        }
        /* .br_new_compare .br_left_table th {
            padding-bottom: 30px !important;
        } */
        /* .br_new_compare .br_right_table tr {
            padding-top: 35px !important;
        } */
        /* .br_new_compare .br_right_table td:first-child {
            padding: 0 !important;
        } */
        .br_new_compare .br_left_table tr {
            padding-bottom: 70px !important;
            display: block;
        }
         .br_new_compare .br_left_table tr:first-child {
            height: 363px !important;
        }
        .br_new_compare .br_left_table tr:nth-child(2) { 
            padding: 0 !important;
        }
        div.br_new_compare .br_right_table tr td {
            padding-bottom: 50px !important;
        }
        div.br_new_compare .br_right_table tr:first-child td, div.br_new_compare .br_right_table tr:nth-child(2) td  {
            padding-bottom: 0 !important;
        } 
        div.br_new_compare .br_right_table tr:nth-child(2) td {
            padding-bottom: 70px !important;
        }
        .br_left_table {
            pointer-events: none;
        }
        div.br_new_compare .br_right_table tr td, div.br_new_compare .br_right_table tr th, div.br_new_compare .br_left_table, div.br_new_compare_block .br_top_table table th, div.br_new_compare_block .br_top_table table td {
            min-width: calc(50vw - 40px) !important;
            width: calc(50vw - 40px) !important;
            max-width: calc(50vw - 40px) !important;
        }
        td .product {
            max-width: 100%;
        }
        /* .br_new_compare .br_left_table tr, .br_new_compare .br_right_table tr {
            display: flex;
        } */
        /* div.br_new_compare .br_right_table tr {
            height: 88px !important;
        } */
    }

    @media screen and (max-width: 480px) {
        .compare__top {
            flex-direction: column;
            align-items: flex-start;
        }
        .compare__top .dropdown {
            display: block;
            width: 100%;
            margin-top: 24px;
            margin-bottom: 24px;
        }
        .compare__top .btn {
            display: none;
        }
        .br_new_compare .br_right_table tr {
            display: flex;
            gap: 14px;
            width: min-content !important;
            margin-bottom: 30px;
        }
        .br_new_compare .br_right_table tr:first-child, .br_new_compare .br_right_table tr:nth-child(2) {
            margin-bottom: 5px;
        }
        div.br_new_compare_block .br_top_table table, div.br_new_compare_block .br_right_table table {
            min-width: initial !important;
        }
        div.br_new_compare .br_right_table tr td, div.br_new_compare .br_right_table tr th, div.br_new_compare .br_left_table, div.br_new_compare_block .br_top_table table th, div.br_new_compare_block .br_top_table table td {
            min-width: calc(50vw - 24px) !important;
            width: calc(50vw - 24px) !important;
            max-width: calc(50vw - 24px) !important;
        }

        .compare .product__title {
            font-size: 11.7391px;
            line-height: 18px;
            height: 36px;
        }
        .compare .product__actions {
            /* flex-direction: column;
            align-items: flex-start; */
            position: relative;
            padding-bottom: 40px;
        }
        /* .page {
            overflow-x: hidden;
            overflow-y: auto;
        } */
        .compare {
            /* margin-right: -20px;
            margin-left: -20px; */
        }
        .compare .product__buy {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            font-size: 14px;
            line-height: 20px;
            padding: 8px 8px;
        }
        .btn.btn-remo {
            right: 0;
            top: 0;
        }
        .br_new_compare .br_right_table p.product__price-old {
            margin-left: 5px !important;
        }
        .compare .product__title::after {
            height: 18px;
        }

        /* .br_right_table {
            padding-left: 20px;
        } */

        .compare .product__tag {
            left: 4px;
            top: 15px;
            font-size: 9px;
            line-height: 13px;
            padding: 4px 9px;
            display: none;
        }
        .compare .product__swiper-wrap {
            /* height: 41vw; */
            margin-bottom: 5px;
        }
        .br_new_compare .br_left_table tr:first-child {
            opacity: 0;
        }
        .br_new_compare .br_right_table tr {
            height: auto !important;
        }
        .br_new_compare .br_left_table tr {
    padding-bottom: 65px !important;
    display: block;
}
        /* .br_new_compare .br_left_table tr:first-child {
            height: 78vw !important;
        }
        .br_top_table-wrap {
            height: 100% !important;
        } */
        .br_new_compare .br_left_table tr:first-child {
    height: 370px !important;
}
    }

</style>


<?php echo do_shortcode('[br_compare_table]');?>



<?php get_footer();