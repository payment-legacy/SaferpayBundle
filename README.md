# PaymentSaferpayBundle

A simple inofficial implementation of the saferpay payment service as a symfony bundle.

[![Build Status](https://api.travis-ci.org/payment/SaferpayBundle.png?branch=master)](https://travis-ci.org/payment/SaferpayBundle)
[![Total Downloads](https://poser.pugx.org/payment/saferpay-bundle/downloads.png)](https://packagist.org/packages/payment/saferpay-bundle)
[![Latest Stable Version](https://poser.pugx.org/payment/saferpay-bundle/v/stable.png)](https://packagist.org/packages/payment/saferpay-bundle)

## installation

### composer.json

    {
        "require": {
            "payment/saferpay-bundle": "2.*"
        }
    }

### app/AppKernel.php

    new Payment\Bundle\SaferpayBundle\PaymentSaferpayBundle(),

### app/config/config.yml (Full config dump - no need to setup this, default values are set)

    payment_saferpay:
        logger:
            serviceid:            logger
        httpclient:
            serviceid:            payment.saferpay.httpclient.buzz
        payinitparameter:
            serviceid:            payment.saferpay.payinitparameter.default
            data:
                accountid:            ~ # insert here your accountid (test-account: 99867-94913159)
                amount:               ~
                currency:             ~
                description:          ~
                orderid:              ~
                vtconfig:             ~
                successlink:          ~
                faillink:             ~
                backlink:             ~
                notifyurl:            ~
                autoclose:            ~
                ccname:               ~
                notifyaddress:        ~
                usernotify:           ~
                langid:               ~
                showlanguages:        ~
                paymentmethods:       ~
                duration:             ~
                cardrefid:            ~
                delivery:             ~
                appearance:           ~
                address:              ~
                company:              ~
                gender:               ~
                firstname:            ~
                lastname:             ~
                street:               ~
                zip:                  ~
                city:                 ~
                country:              ~
                email:                ~
                phone:                ~

## usage

### controller

[See Test SaferpayController](https://github.com/payment/SaferpayBundle/blob/master/Controller/SaferpayController.php)
