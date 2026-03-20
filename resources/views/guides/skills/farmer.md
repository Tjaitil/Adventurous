---
title: Farmer skill
level_requirement: 1
related_guides:
  - skills/farming
  - mechanics/hunger
categories:
  - skills
description: Complete guide to the Farmer proficiency, crops, and workforce efficiency
data:
  - crops
  - workforceUpgrades
tags:
  - proficiency
  - crops
  - farming
  - workforce
---

## Available Crops

<x-guide.crops-table :crops="$crops" />

## Seeds

Seeds for each crop is collected from grown crops. The amount of seeds you get is random. 

## Workforce 
Your crops are tended to by your workforce. The higher **efficiency level** your workers are, the faster your crops will grow. You can upgrade your workforce's efficiency in the <a href="/workforce_lodge">workforce lodge</a>, which will reduce the growth time of all your crops.

### Worker Efficiency Levels

Workers start at efficiency level 1. You can upgrade their efficiency in the <a href="/city_centre">City centre</a> Higher efficiency increases reduces the growth time of crops.

<x-guide.workforce-upgrades-table :upgrades="$workforceUpgrades" />