<?php

function getAverage($data)
{
    return array_sum($data) / count($data);
}

function getRate($dbRates, $moreVotes = 0, $moreSum = 0)
{

    $numberVotes = count($dbRates) + $moreVotes;
    $sum = $moreSum;

    foreach ($dbRates as $key => $values) {
        $sum += $values->score;
    }

    $data['number_votes'] = $numberVotes;
    $data['total_points'] = $sum;
    $data['dec_avg'] = round($data['total_points'] / $data['number_votes'], 1);
    $data['whole_avg'] = round($data['dec_avg']);
    return $data;
}

function getAllRates()
{

    $Rating = new App\Plugin\Rating\Rating();
    $allRating = $Rating->showAll();

    $types = array();
    foreach ($allRating as $rating) {

        if (!array_key_exists($rating->type, $types)) {
            $types[$rating->type] = array();
        }

        if (!array_key_exists($rating->typeId, $types[$rating->type])) {
            $types[$rating->type][$rating->typeId]['score'] = '';
            $types[$rating->type][$rating->typeId]['nbVotes'] = '';
            $types[$rating->type][$rating->typeId]['average'] = '';
        }

        $types[$rating->type][$rating->typeId]['score'] += $rating->score;
        $types[$rating->type][$rating->typeId]['nbVotes']++;
        $types[$rating->type][$rating->typeId]['average'] = round(
            $types[$rating->type][$rating->typeId]['score'] / $types[$rating->type][$rating->typeId]['nbVotes'], 1);
    }

    return $types;
}

function showRatings($type, $typeId)
{
    return '<div class="movie_choice">
                <div id="item-' . $typeId . '" data-type="' . $type . '" class="rate_widget">
                    <div class="star_1 ratings_stars"></div>
                    <div class="star_2 ratings_stars"></div>
                    <div class="star_3 ratings_stars"></div>
                    <div class="star_4 ratings_stars"></div>
                    <div class="star_5 ratings_stars"></div>
                    <div class="total_votes">...</div>
                </div>
            </div>';
}

function showLetRatings($type, $typeId)
{
    return '<div class="movie_choice">
                <div id="item-' . $typeId . '" data-type="' . $type . '" class="rate_widget">
                    <div class="star_1 ratings_stars starClick"></div>
                    <div class="star_2 ratings_stars starClick"></div>
                    <div class="star_3 ratings_stars starClick"></div>
                    <div class="star_4 ratings_stars starClick"></div>
                    <div class="star_5 ratings_stars starClick"></div>
                    <div class="total_votes">...</div>
                </div>
            </div>';
}