Multi-Site Wi-Fi Voucher Vending System
Tech Stack
Backend: Node.js, MySQL, Redis, Prisma ORM (JS)
Frontend: Next.js, React Toolkit (JS), TailwindCSS, Lucide Icons
Owner (Platform) Module
Used by the system owner to manage the entire SaaS platform.
Features:
Create, edit, activate, or deactivate sites.
View all sites and their statuses.
Access system-wide sales reports.
View revenue and fees across all sites.
Configure customer and merchant fee structures per site.
Configure fee sharing rules between stakeholders:
Owner
Payment provider
Developer
Other parties
View financial summaries per site.
Monitor system activity and usage.
Technical Notes:
Owner users have no site assignment.
RBAC enforcement for all admin actions.
Fee sharing handled automatically after successful transactions.
Site Administration Module
Each client (site) has its own management environment.
Features:
Site dashboard with sales and activity summary.
Manage site information and settings.
Create and manage packages (voucher categories).
View site-level sales reports.
View voucher inventory.
Access site financial reports and ledger.
Balances:
digitalSalesBalance: Affected by digital/mobile money sales.
cashSalesBalance: Affected by agent cash collection, reconciled by supervisor.
User & Role Management Module
Role-based access control within each site.
Roles:
Supervisor
Manager
Sales Agent
Features:
Create, edit, activate, or deactivate users.
Assign roles and permissions.
Reset user passwords.
View user activity logs.
Role Responsibilities:
Supervisor
Create and manage Sales Agents and Managers.
Upload voucher batches.
Manage packages.
View all site reports.
Reconcile agent cash balances (reduces cashSalesBalance).
Manager
Create and manage Supervisors and Sales Agents.
View all site reports.
Access financial summaries.
Cannot upload vouchers or sell directly.
Sales Agent
Request vouchers for customers.
View only their own sales history.
Collect cash for on-site sales (affects agent cash balance).
Voucher Inventory Module
Handles voucher stock for each site.
Features:
Upload vouchers in bulk (CSV or text list).
Manual voucher entry.
Automatic assignment of vouchers during sales.
Voucher status tracking: Unused, Sold, Expired.
View voucher inventory by package.
Low-stock alerts.
Package Management Module
Used to define voucher categories.
Features:
Create and edit packages.
Set package name, price, description.
Activate or deactivate packages.
View sales per package.
Agent Sales Module (On-Demand Vouchers)
Used by sales agents to sell vouchers to walk-in customers.
Features:
Select package.
Request a voucher from the system.
Voucher displayed only at time of sale.
Automatic voucher assignment.
Record agent, package, time, site for each sale.
View personal sales history.
Cash collected increases agent cash balance; reconciled by supervisor.
Online Customer Sales Module
Used by customers purchasing vouchers directly.
Features:
Public purchase page per site (e.g., cheetahnet.co.ug/mbarara).
Package selection.
Phone number entry.
Mobile money payment.
Automatic voucher assignment after payment.
Voucher displayed on screen; optional SMS delivery.
Payment Integration Module
Handles mobile money transactions.
Features:
Integration with mobile money providers.
Payment request sent to customer phone.
Real-time payment confirmation.
Automatic voucher issuance after payment.
Transaction fields include:
amount → appears on site ledger and digital balance
customerFee → sent to provider on top of amount
siteFee → deducted from site after collection
totalFee = customerFee + siteFee
toAmount = amount + totalFee
Transaction status tracking: Pending, Successful, Failed.
Fees are credited to a fee account, distributed automatically to fee share parties.
Platform mobile money GL account debited, site payable credited.
Reporting Module
Agent Reports:
Personal sales history.
Daily and weekly totals.
Site Reports:
Sales per agent and per package.
Daily, weekly, and monthly reports.
Voucher usage reports.
Revenue summaries.
Owner Reports:
Sales per site.
Total platform sales.
Fee collections.
Top-performing sites.
Financial & Accounting Module
Maintains financial records per site.
Features:
Separate ledger for each site.
Automatic account entries for every transaction.
Record sales, fees, adjustments, payouts.
Running balance per site.
Financial summaries and statements.
Ledger Notes:
Both digitalSalesBalance and cashSalesBalance are tracked.
Fee accounts are credited on transaction and distributed to fee share party accounts.
Fee Configuration Module
Defines how fees apply per site.
Features:
Configure customer fees: fixed or percentage (sent to provider).
Configure site fees: fixed or percentage (deducted from site after collection).
Apply different fee structures per site.
Fee Sharing Module
Defines how collected fees are distributed.
Features:
Configure stakeholders for each site.
Define share amounts or percentages.
Support multiple parties: Platform Owner, Payment Provider, Developer, Other partners.
Automatic fee distribution logic per transaction.
Each share party has its own account for ledger credit.
Settlement (Site Cash-Out) Module
Enables each site to request and receive payouts of mobile money–collected funds.
Cash collected by agents is not included in system settlements and is reconciled manually.
Features:
View available digital balance.
Request settlements for eligible funds.
Track payout status.
Maintain accurate financial records.
Settlement Workflow:
Request: Site submits a request; system checks digital balance, thresholds, restrictions.
Review: Owner approves, rejects, or adjusts amount.
Processing: Approved payout sent via mobile money.
Completion: Ledger updated, digital balance reduced.
Statuses: Pending Approval, Approved, Processing, Completed, Rejected, Failed.
Owner Controls: View all requests, filter, approve/reject, enter payout reference, adjust amounts, add notes.
Site Reports: Show available balance, pending/ completed settlements, total settled amount.
Cash sales by agents are tracked separately in the ledger but not included in digital balance settlements.