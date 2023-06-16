<?php

class GameConstants
{

    public const WARRIOR_LOCATIONS = [
        "cruendo",
        "tasnobil",
    ];

    public const WARRIOR_TYPES = [
        "melee",
        "ranged"
    ];

    public const CROP_LOCATIONS = [
        "krasnur",
        "towhar",
    ];

    public const MINE_LOCATIONS = [
        "golbak",
        "snerpiir",
    ];

    public const SKILL_ACTION = [
        "mine",
        "crops"
    ];

    public const SKILLS = [
        "crops",
        "miner",
        "trader",
        "warrior",
    ];

    public const WRONG_LOCATION_ERROR = "You are in the wrong location!";

    public const TRADER_SKILL_NAME = "trader";

    public const MINER_SKILL_NAME = "miner";

    public const FARMER_SKILL_NAME = "farmer";

    public const WARRIOR_SKILL_NAME = "warrior";

    public const ADVENTURER_SKILL_NAME = "adventurer";

    public const CURRENCY = "gold";

    public const DIPLOMACY_LOCATIONS = ["hirtam", "pvitul", "khanz", "ter", "fansalplains"];

    public const DATE_FORMAT = "Y-m-d H:i:s";

    public const MINER_STORE_DISCOUNT = 0.20;
}


const WARRIOR_LOCATIONS = [
    "cruendo",
    "tasnobil",
];

const WARRIOR_TYPES = [
    "melee",
    "ranged"
];

const CROP_LOCATIONS = [
    "krasnur",
    "towhar",
];

const MINE_LOCATIONS = [
    "golbak",
    "snerpiir",
];

const SKILL_ACTION = [
    "mine",
    "crops"
];

const TRADER_SKILL_NAME = "trader";

const MINER_SKILL_NAME = "miner";

const FARMER_SKILL_NAME = "farmer";

const WARRIOR_SKILL_NAME = "warrior";

const ADVENTURER_SKILL_NAME = "adventurer";

const SKILLS = [
    TRADER_SKILL_NAME,
    FARMER_SKILL_NAME,
    WARRIOR_SKILL_NAME,
    MINER_SKILL_NAME,
    ADVENTURER_SKILL_NAME
];

const WRONG_LOCATION_ERROR = "You are in the wrong location!";

const CURRENCY = "gold";

const DIPLOMACY_LOCATIONS = ["hirtam", "pvitul", "khanz", "ter", "fansalplains"];

const DATE_FORMAT = "Y-m-d H:i:s";

const MINER_STORE_DISCOUNT = 0.20;

const WARRIOR_REST_PER_MINUTE = 3;

const STAMINA_SKILL_NAME = "stamina";
const TECHNIQUE_SKILL_NAME = "technique";
const PRECISION_SKILL_NAME = "precision";
const STRENGTH_SKILL_NAME = "strength";


const WARRIOR_SKILLS = [
    STAMINA_SKILL_NAME,
    TECHNIQUE_SKILL_NAME,
    PRECISION_SKILL_NAME,
    STRENGTH_SKILL_NAME
];

const WARRIOR_TRAINING_TYPES = [
    "general",
    ...WARRIOR_SKILLS
];

const MAX_INVENTORY_AMOUNT = 18;
const MAX_STOCKPILE_AMOUNT = 60;
