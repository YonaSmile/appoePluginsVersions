<?php

function getAverage($data)
{
    return array_sum($data) / count($data);
}

function getRate($dbRates, $moreVotes = 0, $moreSum = 0)
{
    $data = array();
    $numberVotes = count($dbRates) + $moreVotes;
    $sum = $moreSum;

    if ($numberVotes > 0) {
        foreach ($dbRates as $key => $values) {
            $sum += $values->score;
        }

        $data['number_votes'] = $numberVotes;
        $data['total_points'] = $sum;
        $data['dec_avg'] = round($data['total_points'] / $data['number_votes'], 1);
        $data['whole_avg'] = round($data['dec_avg']);
    }
    return $data;
}

function getAllRates($status = 1)
{

    $Rating = new \App\Plugin\Rating\Rating();
    $allRating = $Rating->showAll(false, $status);

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

function getUnconfirmedRates()
{
    $Rating = new \App\Plugin\Rating\Rating();
    return $Rating->showAll(false, 0);
}

function showRatings($type, $typeId, $clicable = true, $sizeClass = 'largeStars', $minimize = false)
{
    $html = '<div class="movie_choice">
                <div id="' . strtoupper($type) . '-item-' . $typeId . '" data-type="' . $type . '" class="rate_widget">
                    <div class="star_1 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_2 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_3 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_4 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_5 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>';
    if (!$minimize) {
        $html .= '<div class="total_votes" >...</div>';
    }
    $html .= '</div></div>';

    return $html;
}

function getObj($type)
{

    switch ($type) {
        case 'ITEMGLUE':
            return new \App\Plugin\ItemGlue\Article();
            break;
        case 'CMS':
            return new \App\Plugin\Cms\Cms();
            break;
        case 'SHOP':
            return new \App\Plugin\Shop\Product();
            break;
        default:
            return false;
    }
}