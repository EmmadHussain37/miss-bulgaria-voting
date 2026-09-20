# Miss Bulgaria Voting

A complete WordPress voting platform built for beauty pageants,
competitions, and online contests.

Miss Bulgaria Voting allows administrators to manage contestants, create
voting packages, accept free and paid votes, process Stripe payments
securely, and display live candidate rankings.

# Features

## Candidate Management

-   Create and manage contestants
-   Add contestant profiles and images
-   Track candidate votes
-   Display ranking positions
-   Manage multiple contestants

## Voting System

-   Free voting system
-   Secure REST API voting
-   WordPress nonce verification
-   IP-based duplicate vote protection
-   24-hour voting restriction
-   Bot protection using honeypot validation
-   Candidate validation before voting

## Paid Voting System

-   Stripe Checkout integration
-   Custom vote packages
-   Secure payment processing
-   Stripe webhook verification
-   Automatic vote allocation after successful payment
-   Duplicate payment protection
-   Transaction tracking

## Leaderboard

-   Live candidate ranking
-   Vote count display
-   Automatic sorting by votes
-   Public leaderboard display

## Admin Dashboard

The admin dashboard provides:

-   Total candidates
-   Vote package statistics
-   Total votes
-   Revenue tracking
-   Transaction overview

# Security Features

-   WordPress REST nonce verification
-   Stripe webhook signature validation
-   Duplicate payment protection
-   Candidate validation
-   Vote package validation
-   IP-based voting protection
-   Honeypot bot protection

# Requirements

-   WordPress 6.0+
-   PHP 8.0+
-   Stripe account (for paid voting)

# Installation

1.  Download the plugin.

2.  Upload the plugin folder to:

/wp-content/plugins/

3.  Activate the plugin from:

WordPress Dashboard → Plugins

4.  Configure settings from:

Miss Bulgaria Voting → Settings

# Stripe Setup

1.  Add your Stripe Secret Key.

2.  Add your Stripe Webhook Secret.

3.  Configure your webhook endpoint:

https://yourwebsite.com/wp-json/mbv/v1/stripe-webhook

4.  Enable Stripe event:

checkout.session.completed


# Shortcodes

## Candidates Voting Page

Use this shortcode to display all candidates and the voting interface:

[miss_bulgaria_candidates]

## Leaderboard Page

Use this shortcode to display the candidate rankings:

[mbv_leaderboard]


# REST API Endpoints

## Submit Vote

POST /wp-json/mbv/v1/vote

## Create Stripe Checkout

POST /wp-json/mbv/v1/create-checkout

## Stripe Webhook

POST /wp-json/mbv/v1/stripe-webhook

## Leaderboard

GET /wp-json/mbv/v1/leaderboard

# Database

The plugin creates custom database tables.

## Votes Table

Stores:

-   Candidate ID
-   Vote amount
-   IP address
-   Vote timestamp

## Transactions Table

Stores:

-   Candidate ID
-   Package ID
-   Stripe payment ID
-   Payment amount
-   Transaction status

# Project Structure

miss-bulgaria-voting/

-   includes/
-   templates/
-   assets/
-   vendor/
-   miss-bulgaria-voting.php

# Future Improvements

-   Advanced fraud detection
-   Email verification voting
-   Social media voting
-   Multi-language support
-   More payment gateways
-   Advanced analytics dashboard

# License

This project is provided for development and educational purposes.

# Author

Developed by Emmad Hussain

GitHub: https://github.com/EmmadHussain37
