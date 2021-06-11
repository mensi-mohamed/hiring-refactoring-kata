# Mathilde Grise - Refactoring Kata

## Introduction

Customers can make a reservation on a product on the online shop.
This feature is quite old as the code. Unfortunately as business grows our development team encounters 
more and more difficulties to evolve and maintain the code.
Business requirements change quite often and there is not enough tests to be confident.
Code uses internally our custom framework.
And as if that wasn't enough, the application is tightly coupled to third services over the network.
Sometimes outages that are not dependent on our will make reservation impossible.
Our system is not resilient on that matter.

Mathilde Grise operates in the luxury industry.
Each reservation Mathilde Grise can not fulfill could be a huge loss of income.
Things can't go on like this, you have been chosen to improve the situation. 

## The code

The main class you should focus on is `CreateReservation`.
It contains the logic to make a reservation on a product.
The main method is `create`. It is 73 lines long and it does too many things.
There is worse but you can do much better.
It takes an `array` of data and returns a `Response`.

There is only one test. You can use it as entrypoint if you want.
Be careful tho the test fails.

## Rules

* You can modify everything you want except when comments explicitly forbid it.
* There is no limit in time. However even a day could not be enough to improve everything. 
  We are aware it can represent a significant amount of work that is why we suggest you to take between 1 and 2 hours to do your best.
* As `create` method is the entrypoint, you don't know how it is used across the application. 
  However we allow you to change the signature if you think it could help. 
  Keep in mind we expect you to explain your choice.

## What we expect from you

Overall, the code should be much more easier to maintain and evolve.
Keep in mind that coding is a human practice through writing **AND** reading.
The easier we can understand what you did the better it is.
That's why we expect you to explain the choices you made the way you see fit best.

We do not expect from you to improve or rewrite the framework around the business code.
We do not expect as well to see complex infrastructure and implementation.

Finally, as we said before, it takes to long to fix everything. 
So it's perfectly fine to explain what you have in mind to improve quality.
But be careful we value priority order. 
There are things that are easy to do and could improve a lot the code.

## Deliverables

- A git history we can look into.

## Helper

There is simple `Makefile` to help you to install dependencies and run the tests.
To prevent you to install build and runtime environment on your host, it uses Docker.
Feel free to use anything else.

Run: 
`make composer`

Then inside the container run: `composer update`

Finally exit and run: `make tests`

# Good Luck and Have Fun !