<?php require('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="bigTitle"><?= trans('Évaluation'); ?></h1>
            <hr class="my-4">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class='movie_choice'>
                Rate: Raiders of the Lost Ark
                <div id="r1" class="rate_widget">
                    <div class="star_1 ratings_stars"></div>
                    <div class="star_2 ratings_stars"></div>
                    <div class="star_3 ratings_stars"></div>
                    <div class="star_4 ratings_stars"></div>
                    <div class="star_5 ratings_stars"></div>
                    <div class="total_votes">vote data</div>
                </div>
            </div>

            <div class='movie_choice'>
                Rate: The Hunt for Red October
                <div id="r2" class="rate_widget">
                    <div class="star_1 ratings_stars"></div>
                    <div class="star_2 ratings_stars"></div>
                    <div class="star_3 ratings_stars"></div>
                    <div class="star_4 ratings_stars"></div>
                    <div class="star_5 ratings_stars"></div>
                    <div class="total_votes">vote data</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require('footer.php'); ?>