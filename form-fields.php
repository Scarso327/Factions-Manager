<?php
return array(
    // Generic Inputs
    'Input' => array('Input', 'Allows the input of anything. Best for short paragraphs.'),
    'Textarea' => array('Textarea', 'Allows the input of anything like the Input Field but is better for larger paragrahps.'),
    // Global Dropdowns
    'factionMembers' => array('Members Dropdown', 'Creates a dropdown full of every member of this faction.'),
    'factionRanks' => array('Ranks Dropdown', 'Fills a dropdown with all the ranks within this faciton.'),
    'factionSections' => array('Section Dropdown', 'Populates a dropdown with all the sections this faction has.'),
    // Member Specific Dropdowns (Require redefined steamid)
    'memberRanks' => array('Members\' Ranks Dropdown', 'Creates a dropdown of ranks like the Ranks Dropdown but excludes the one for the given member.'),
    'memberSections' => array('Members\' Section Dropdown', 'Like Section Dropdown but excludes the one this member is currently in.'),
    'memberRanksAbove' => array('Members\' Higher Ranks Dropdown', 'Works like the Members\' Ranks Dropdown but only includes the ones above our current.'),
    'memberRanksBelow' => array('Members\' Lower Ranks Dropdown', 'Works like the Members\' Ranks Dropdown but only includes the ones below our current.')
);