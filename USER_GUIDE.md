# Library Management System — Comprehensive User Guide

This handbook is for staff and administrators who are new to the library
system. It explains what each module is for, how to use it, and why some
accounts see different menus or actions.

The application saves information as you submit forms. A page that displays
**Save**, **Submit**, or **Send** has not recorded your changes until you
submit it and see a success message or the updated record.

## How the system is organized

### A simple way to think about the system

The application has separate work areas because they track different kinds of
library activity:

- **Visitors** records how many people visited.
- **Newsletters** records counts for named newspaper titles.
- **Report** brings together detailed activity recorded by different officers.
- **Mail** lets staff communicate about work without using personal email.
- **Publication** keeps shared planning information, proposals, and meeting
  records.
- **Settings** is for a small number of administrative and chatbot-training
  tasks.

Use the module that matches the information you are recording. A newsletter
count is not a visitor count, a calendar event is not a proposal, and a
proposal's recorded destination is not the same as submitting it through this
system to an outside organization.

## 1. Getting started

### Sign in

1. Open the web address supplied by your library or system administrator.
2. Enter your registered email and password, then choose **Log in**.
3. If you have forgotten your password, choose **Forgot your password?** and
   follow the instructions sent to your registered email.
4. If registration is enabled, choose **Register here** to create an account.
   Registration asks for your name, email, password, and password
   confirmation. New accounts start in the administrative staff category; this
   does not make the account an administrator. Ask an administrator to correct
   the category if needed and grant permissions before using restricted tools.
5. To leave the system safely, open your name in the navigation bar and choose
   **Log Out**.

The sign-in page also displays rotating library announcements and motivational
messages. The message and announcement change together every 30 seconds.

### Finding your way around

After you sign in, the navigation bar provides links to the system's main
modules:

| Navigation item | What it is for |
| --- | --- |
| **Visitors** | Record visitor counts and review a quick summary and chart. |
| **Newsletters** | Record counts for newspapers and newsletters. |
| **Report** | Review detailed visitor and newsletter records, filter them, and export reports. |
| **Mail** (envelope icon) | Send private messages to staff and read replies. |
| **Publication** | Open the calendar, proposals, and relevant meeting records. |
| **Settings** | Open chatbot training or user administration when your account is allowed to. |
| **Your name** | Sign out of the system. |

Menu visibility depends on your staff category and permissions. If a link is
missing, see [Accounts, roles, and permissions](#9-accounts-roles-and-permissions).
The main work pages use the library navigation bar. A separate account
dashboard and profile page are also available after sign-in; the dashboard is
a basic welcome page rather than a visitor or newsletter report.

### A typical staff workflow

At the end of a shift or whenever your local procedure requires it:

1. Sign in with your own account.
2. Record the visitor count you are responsible for.
3. Record newsletter counts in their separate module, if that is part of your
   duties.
4. Check your summary to catch an obvious data-entry mistake.
5. Use Report when you need to investigate a date or compare entries across
   officers.
6. Check Mail for work messages and review any upcoming Calendar items.
7. Log out when you finish.

Follow your library's local procedure for when counts should be entered and
who should verify them. The system records an entry under the account that
submits it.

## 2. Important terms

- **Module:** One part of the application for a particular activity. For
  example, the Visitors module records visits and the Calendar module records
  upcoming events.
- **Staff category:** The account's assigned category, such as academic or
  administrative. Categories usually determine which meeting records staff can
  see; an explicit meeting permission can grant additional access.
- **Permission:** An additional access setting assigned by an administrator.
  For example, a user can be given permission to manage proposals without
  becoming an administrator.
- **Summary:** Totals grouped into time periods, such as today or this month.
- **Detailed report:** Individual date-and-officer rows that help explain how
  totals were recorded.
- **Attachment:** A file uploaded to accompany a record, such as a PDF proposal
  or meeting minutes.
- **Archive:** Deactivate a user's normal access without deleting their
  historical visitor records.

## 3. Account profile and security

The Profile page is for your own login account, not for editing another
person's permissions. Open `/profile` while signed in (or choose **Profile**
from the account menu when that navigation is shown).

- **Profile Information:** Change your display name or email address, then
  choose **Save**. If your email changes, the system marks it unverified.
- **Update Password:** Enter your current password, new password, and
  confirmation, then save.
- **Delete Account:** This permanently deletes your account after you confirm
  with your password. This is not the same as an administrator archiving an
  account. Associated visitor and newsletter records may also be deleted, so
  do not use this option to solve a login or permission problem. Contact an
  administrator instead.

Use the **Forgot your password?** link on the sign-in page if you cannot sign
in and do not know your current password.

## 4. Visitors module

### What it does

The Visitors page (`/reports`) is used to record library visits. Its summary
shows counts grouped into periods, and its chart visualizes today's recorded
male and female totals. These are operational counts, not a list of individual
visitors.

### Record one visitor

1. Open **Visitors**.
2. In the counting card, choose **Count**.
3. Choose **Male** or **Female** from the list.
4. Submit the count.
5. Wait for the success message and the updated page before recording another
   visit.

Each successful submission adds one visit to the selected category.

### Record several visitors at once

1. Open **Visitors**.
2. Press the **Shift** key to reveal the custom-count form.
3. Enter the number of male visitors and female visitors. Use whole numbers;
   leave a field blank if that category has no visitors.
4. Choose **Submit** once.
5. Wait for the page to update before entering another batch.

**Shift opens the custom form; it does not submit a count.** The system accepts
new Male and Female entries. Some historical records may show categories that
are no longer available for new submissions.

### Read the summary and chart

The summary on this page is for the currently signed-in account; it is not the
all-officer comparison. The detailed Report page is the place to review
records grouped by date and officer.

The summary groups recorded visits into:

- **Today** — entries recorded today.
- **Yesterday** — entries recorded yesterday.
- **This Week** — entries in the current calendar week.
- **This Month** — entries in the current calendar month.
- **Last Month** — entries in the previous calendar month.
- **Last Three Months** — entries in the displayed three-month reporting
  period.
- **TOTAL VISITS** — the all-time total recorded under your account.

The chart offers a visual comparison of today's male and female counts. If no
visits have been recorded for today, the chart indicates that there is no data
yet. The summary's total can include historical categories that are not shown
as separate columns; for that reason it may not always equal only the visible
Male and Female values.

### Correct a visitor count carefully

The minus buttons beside today's values are correction controls. In the
current implementation, a minus action removes a matching current-day counter
record; it does **not** subtract exactly one person from the total. The action
may remove a record submitted by another officer. Do not use it as a routine
one-person correction. Contact an administrator if a count needs correction.

## 5. Newsletters module

### What it does

The Newsletters page records quantities for newspaper titles. It provides
your summary by reporting period. Use this module for the newsletter counts
your library collects; it is separate from visitor counting.

### Record a newsletter count

1. Open **Newsletters**.
2. Choose **Count** to open the entry controls.
3. Select a title, such as Daily News, Guardian, Nipashe, Uhuru, Habari Leo,
   Citizen, East African, or Mwananchi.
4. Enter the number of copies counted.
5. If a date field is shown, select the date the count belongs to.
6. Submit the form and check that the page updates.

If a title is not selected, its count controls may remain hidden. Enter the
correct title and quantity before submitting. Avoid submitting an empty or
zero count when you mean to record actual copies.

### Review newsletter totals

The summary on the Newsletters page is for the currently signed-in account.
Use **Report** when you need to compare newsletter entries recorded by
different officers.

The summary table groups the counts by today, yesterday, week, month, previous
month, last three months, and total. Use **Report** in the top navigation to
review the detailed newsletter report by date and officer.

## 6. Report module

### What it does

**Report** (page `/visitor_stats`) provides detailed tables for visitor and
newsletter activity. Unlike the Visitors and Newsletters summary cards, it
helps you inspect recorded results by date and officer. The page also includes
charts for examining visitor patterns over time.

The report is a combined view of staff activity, not just your personal
summary. A row is grouped by date and officer so you can see which account
recorded the counts. Reports are for review and export; use Visitors or
Newsletters to make new entries.

### Use the report tabs

1. Open **Report**.
2. Choose **Visitors Report** or **Newsletter Report**.
3. Open or use the **Filters** area.
4. Choose a date range in **Filter by Date** to narrow the displayed results.
5. Review the rows and charts. Visitor rows include the date, officer, gender
   counts, and total; newsletter rows include the date, officer, title counts,
   and total.
6. Choose the appropriate **Export to Excel** button to export the report.

If a report appears empty, clear the date filter or select a wider date range.
The detailed report is different from the period summary: the detailed report
helps identify the underlying dates and officers.

## 7. Mail module

### What it does

Mail is an internal messaging tool for staff. It is separate from external
email. Conversations are visible to their participants; an administrator does
not automatically become a participant in a private conversation.

### Find and read messages

- Select the **envelope** icon to open your inbox.
- The number on the icon indicates unread messages.
- Select a conversation's subject to read its messages and any permitted
  attachment.
- Opening an unread conversation marks the messages addressed to you as read.
- Choose **Sent** in the mail sidebar to review messages you sent.

### Send a new message

1. Open Mail and choose **Compose**.
2. Search for staff by name or email and select one or more **To** recipients.
   At least one recipient is required.
3. Open **Add CC or BCC** if you need additional recipients:
   - **CC** recipients are visible to other recipients.
   - **BCC** recipients are hidden from other recipients.
4. Enter a subject and message.
5. Optionally attach a file (maximum 10 MB). Supported file types include PDF,
   Office documents, text files, and common image formats.
6. Choose **Send message**.

### Reply to a conversation

Open the conversation, write in the reply field, optionally attach a file, and
choose **Send reply to all**. Replies go to the latest message's To and CC
recipients. BCC recipients remain private. If other participants are no longer
active, replying may be unavailable.

## 8. Publication modules

**Publication** groups tools for schedules, proposals, and formal meeting
records. The available submenus depend on your category and permissions.

### 7.1 Library Calendar

#### Purpose

The calendar is a shared schedule of library events, holidays, and other
schedules. It is for planning and reference, not for visitor-count entry.

#### Browse the calendar

1. Open **Publication → Library Calendar**.
2. Use the left and right arrows to move between months.
3. Choose **Today** to return to the current month.
4. Select a date to view the activities on that date.
5. Review the list beneath the calendar for the selected month's items.

Weekdays (Monday through Friday) are marked as working days. Past dates and
their existing items remain viewable.

#### Add or update an item

Only an administrator or a user with **Manage library calendar** permission
can create, edit, or remove items.

1. Choose **Add calendar item** (or **Edit** beside an existing item).
2. Enter a title and select a type: library event, holiday, or other schedule.
3. Enter start and end dates and times. New items must start today or later.
4. Optionally enter a location and description, or mark the item as all day.
5. Choose **Save calendar item**.

When editing a historical item, its existing past start can remain unchanged;
you cannot create a new item in the past.

### 7.2 Proposal Management

#### Purpose

Proposal Management is a shared place to track proposals, their planned
timelines, submission destinations, details, and PDF documents. It is not the
same as submitting a proposal to an external organization; the recorded
destination tells staff where the proposal is intended to go.

#### Find and read a proposal

1. Open **Publication → Proposal Management**.
2. Search by title, or enter a start/end range to find proposals whose
   timelines overlap the selected dates.
3. Choose **Filter** or type into the search field; the results update on the
   page.
4. Review the title, timeline, destination, details, and submission date.
5. Choose **Download** in the PDF column to retrieve the attached document.

Choose **Clear** to remove the search and date filters.

#### Submit a proposal

Administrators and staff with **Manage proposals** permission can submit
proposals.

1. Choose **Submit Proposal**.
2. Enter a clear proposal title.
3. Enter the timeline's **From** and **To** dates. The end date must not be
   earlier than the start date.
4. Enter where the proposal should be submitted (an office, email address, or
   URL) and provide the proposal details.
5. Attach the proposal as a PDF and choose **Submit Proposal**.
6. Return to the proposal list and confirm the entry is present.

#### Automatic deadline reminders

The system places an internal Mail message in each academic-category staff
member's inbox seven days and three days before a proposal's end date. The
message comes from **Library System** and includes the proposal title, deadline,
and recorded submission destination. These reminders do not use Gmail, SMTP, or
an external email service.

To find a reminder, select the **envelope** icon to open your inbox. The unread
count on the icon increases when a reminder arrives. Open the conversation with
the proposal title to read the deadline and where to submit. These are
automatically generated notices, so replying is not available.

The reminders are created by the system's scheduled task. If a reminder is
missing, contact the administrator to check that the task is running; do not
submit the proposal again just to trigger a reminder.

### 7.3 Meeting minutes

#### Purpose

Meeting records preserve the meeting topic, date, place, attendance, minutes,
and optional documents or audio. The system separates **Administration** and
**Academics** meeting sections so staff can work with the records for their
category.

#### View and filter minutes

1. Open the appropriate submenu under **Publication**:
   - **Administration → Meeting Management** for administrative meetings.
   - **Academics → Meeting Minutes Management** for academic meetings.
2. Enter a **From date** and/or **To date**, then choose **Filter**. Leave both
   blank to show all matching meetings.
3. Review the meeting details and attendance.
4. Use **Download PDF** for attached minutes or the audio player to listen to
   an attached recording. If available, **Open recording** opens the audio
   separately.

Staff can view meetings in their category. Administrators can access both
sections. Only an administrator or a user granted management permission for
that section can add, edit, or remove its records.

#### Record or edit meeting minutes

1. Choose **Record meeting** or **Record academic meeting**.
2. Enter the meeting title/topic, date and time, and venue.
3. In **Attendance**, search for staff, check the attendees, and select each
   person's status: Present, Absent, or Absent with permission.
4. Enter the meeting minutes.
5. Optionally attach a PDF of the minutes (maximum 10 MB).
6. Optionally attach an audio file (MP3, WAV, OGG, WebM, or M4A, maximum 50 MB)
   or choose **Start recording** and allow microphone access. Choose **Stop
   recording** before saving.
7. Save the meeting and confirm it appears in the meeting list.

When editing, uploading or recording a replacement file replaces the existing
attachment of that type. Leave the file input unchanged to keep the current
attachment.

### 7.4 Barcode generator

#### Purpose

The barcode tool creates printable CODE39 labels from a sequence of numbers.
It does not register inventory items in the system; it generates labels for
you to print and use.

#### Generate labels

1. Open **Publication → Administration → Barcode**.
2. Enter the title that should appear on the labels.
3. Enter a prefix and the starting and ending numbers.
4. Set the padded length (for example, padding length 4 displays number 7 as
   `0007`).
5. Choose **Generate** to preview the labels.
6. Choose **Export PDF** to download a printable PDF.

The start number must be at least 1, the ending number must not be earlier than
the start, and the range is limited to 5,000 labels per generation.

## 9. Accounts, roles, and permissions

### How access works

Signing in confirms your identity; it does not automatically grant every
management task. A **role** describes the broad kind of account you have
(administrator, academic staff, or administrative staff). A **permission**
adds or removes a particular capability. The system checks your administrator
status, staff category, and any permissions assigned to your account.

- **Administrator:** Can access administrator tools, both meeting sections,
  user management, and Settings.
- **Academic staff:** Can view academic meeting records.
- **Administrative staff:** Can view administrative meeting records.
- **Permission holder:** Can manage only the specific module or meeting
  section granted by an administrator.
- **Read-only staff:** Can view the records allowed by their category but may
  not see buttons to create, edit, or remove them.

The **View Settings navigation** permission only makes the Settings menu
visible. It does not grant access to chatbot training or user management. Each
tool has its own access check. Administrators always see Settings; **Manage
Users** remains an administrator-only tool.

### What administrators can assign

Open **Settings → Manage Users**, find the person, and choose **Edit**. An
administrator can update account details, staff category, password, and these
permissions:

| Permission | What it allows |
| --- | --- |
| **Manage academic meeting minutes** | Create, edit, and remove academic meeting records. |
| **Manage administrative meeting minutes** | Create, edit, and remove administrative meeting records. |
| **Manage proposals** | Submit proposals. |
| **Manage library calendar** | Add, edit, and remove calendar items. |
| **Train chatbot** | Add chatbot examples and retrain the model. |
| **View Settings navigation** | Display the Settings menu; it grants no tool access by itself. |

To grant access, check the appropriate box and choose **Save changes**. To
remove access, uncheck it and save. Administrators can archive another user
from the user list; they cannot archive their own account. Archiving preserves
historical visitor records.

If a user needs two tasks, grant both relevant permissions. For example, a
chatbot trainer also needs **View Settings navigation** to see Settings in the
menu; neither permission substitutes for the other.

## 10. Chatbot and chatbot training

### Ask the chatbot

On pages with the floating chat button, choose it to open the assistant. Type
a question about visitor counts and submit it. The chatbot uses its trained
language model to interpret the question. It reports counts recorded under
your signed-in account; use **Report** for the all-officer view. Try
straightforward questions such as “How many visitors came this week?” or “How
many female visitors came last month?” If it does not understand, try simpler
wording or ask an administrator for help.

### Add examples and train the model

Only administrators and staff granted **Train chatbot** permission can open
**Settings → Train Chatbot**. A trainer also needs Settings navigation
visibility to see that menu item.

The training page has three parts:

1. **Add a training example:** Enter a realistic message a staff member might
   ask, then choose the intent that best describes what the message means
   (for example, a greeting or a visitor-total question).
2. **Annotate entities (optional):** Enter values such as gender, period, or
   month when they appear in the example text. The value must actually appear
   in the example. For instance, if the example says “female visitors last
   week,” `female` and `last week` can be annotated.
3. **Retrain model:** Save examples first, then choose **Train chatbot model**
   to rebuild the model using the available examples. Wait for a success
   message. If training fails, contact the system administrator.

Use clear, varied example messages that express the same intent naturally.
Avoid labeling a message with an intent that it does not express.

## 11. Common questions and troubleshooting

### I cannot see a menu or button

Your account may not have the needed staff category or permission. Ask an
administrator to check **Settings → Manage Users → Edit**. Some pages are
available to view while their editing buttons are reserved for managers.

### I can open Settings but do not see the tool I need

Settings visibility and tool permissions are separate. Ask the administrator
for the specific permission, such as **Train chatbot**.

### A form says a field is required

Check that you completed all required fields. For example, visitor entry needs
a selected gender; a proposal needs its timeline, destination, details, and
PDF; a meeting needs its title, date/time, venue, and minutes. Use the
attendance controls to record attendance when creating or updating a meeting.

### Why is my total different from the detailed report?

Visitors and Newsletters show totals for your own signed-in account. The
detailed Report combines entries from officers. It can therefore show a
larger all-officer total. Visitor totals may also include historical categories
that are no longer available for new entries.

### Which page should I use?

- To add one or more visits, use **Visitors**.
- To record a newspaper-title count, use **Newsletters**.
- To find who recorded activity on a date, use **Report**.
- To send or reply to a staff message, use **Mail**.
- To view upcoming dates, use **Library Calendar**.
- To add a proposal to the shared list, ask for **Manage proposals** access.
- To record a formal meeting, use the appropriate meeting section if you have
  management access.
- To change a user's role or permissions, the administrator uses
  **Manage Users**.

### My update is not visible

Wait for the success message, then refresh or return to the relevant list.
Check that filters are not hiding the new record. If the operation returned an
error, correct the indicated field and submit again.

### I cannot open a PDF, play audio, or send an attachment

Check that the file is in a supported format and within the page's size limit.
Meeting audio recording also requires browser microphone permission. If the
problem continues, contact the system administrator.

### I cannot sign in

Check the email address and password, use the password-reset link if needed,
and contact the system administrator if the account is not recognized or is
inactive.

## 12. Good practices

- Confirm the current page and account before recording counts.
- Enter a batch once and wait for confirmation to avoid duplicate counts.
- Use accurate dates, titles, attendance, and descriptions so other staff can
  find the information later.
- Check filters before assuming a record is missing.
- Keep attachments relevant and within the stated file type and size limits.
- Treat private mail and BCC recipients appropriately.
- Ask an administrator to grant access rather than sharing passwords or
  another person's account.
- If an action could delete or replace data, read the confirmation and verify
  the record before continuing.
