# Resque Bundle

A bundle for managing jobs with php-resque

[![Latest Stable Version](https://poser.pugx.org/mcfedr/resque-bundle/v/stable.png)](https://packagist.org/packages/mcfedr/resque-bundle)
[![License](https://poser.pugx.org/mcfedr/resque-bundle/license.png)](https://packagist.org/packages/mcfedr/resque-bundle)
[![Build Status](https://travis-ci.org/mcfedr/json-form.svg?branch=master)](https://travis-ci.org/mcfedr/json-form)

## Requirements

You will need a Redis server

You can try something like 

    apt-get install redis-server
    
Or
    
    brew install redis

## Install

### Composer

    php composer.phar require mcfedr/resque-bundle

### AppKernel

Include the bundle in your AppKernel

    public function registerBundles()
    {
        $bundles = array(
            ...
            new mcfedr\ResqueBundle\mcfedrResqueBundle(),

### Configuration

Your configuration should be something like this

    mcfedr_resque:
        host: 127.0.0.1
        port: 6379
        default_queue: default

