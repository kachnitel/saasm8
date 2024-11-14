# SAAS M8

## Description

This tool is meant to help me..I mean software developers to manage time tracking and billing for their projects.

## Features

- Track time entries for projects
- Connect to GitHub issues that can be assigned to a time entry
  - Tags / labels can be used to determine the type of work being done / billing category
- Generate invoices based on time entries
  - Select time range to invoice for
  - Select projects to invoice for (+ categories?)
  - Create a list breaking down each category by time spent
    - includes all issues and links to GitHub

# TODO:
- When category is changed, ask if new sesison should be started. Unless it's not been set yet.
- Reusable |duration filter and Timer component (using js with live sync every x seconds, "active/paused" state)