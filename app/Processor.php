<?php

namespace App;

enum Processor: string
{
    case Givebutter = 'givebutter';
    case Stripe = 'stripe';
}
