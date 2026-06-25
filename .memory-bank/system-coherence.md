# System Coherence Protocol

This document defines the protocols, safety checks, and alignment guidelines required to maintain consistency, coherence, and safety across all AI sessions and agent executions.

## 1. Operating Mode & Environment Discovery

At the start of every session, the active agent must detect its operating mode:
- **CI Mode:** If `CI=true` is set. Runs in a non-interactive, automated environment.
- **Interactive Mode:** If `CI=true` is not set. Requires explicit user gates and approvals for critical decisions and git operations.

## 2. Pre-Flight Checklist

Before proposing or executing any modifications:
1. **Branch Verification:** Check and document the current branch name and commit hash.
2. **Worktree Health Check:**
   - Run `git status` to detect uncommitted changes.
   - *Interactive Mode:* Stop if the worktree is dirty. Prompt user for instruction. Do not proceed until approved.
   - *CI Mode:* Do not block. Record dirty files in `.memory-bank/bugs/bug-list.md` as `Unconfirmed / Environment Warning` and proceed.
3. **Information Integrity Check:**
   - Scan `.memory-bank/` for active-session state and target tasks.
   - Distinguish facts clearly: `Verified` (from codebase/manifests), `Inferred` (derived logically), or `Unconfirmed` (hypothetical/needs checks).

## 3. Post-Flight Checklist & Verification

After any edits are performed:
1. **Verification Runs:** Suggest or run verification commands (e.g. `php -l` for PHP syntax checking).
2. **No Source Modification Policy:** Ensure no application code is touched unless explicitly tasked.
3. **Session Update:** Update `.memory-bank/active-session.json` and `.tasks/pipeline.md` to reflect task statuses.
4. **Git State Review:** Perform `git status` to review changed files. Propose a clean git add list and commit message. Do not commit or stage without approval.

## 4. Unconfirmed Decision Protocol

If a fact or architectural decision cannot be verified from local repository configurations, manifests, or code:
1. Mark the fact/decision as `Unconfirmed`.
2. *Interactive Mode:* If it affects security, public API, deployment, or database structures, stop and ask the user for confirmation.
3. *CI Mode:* Do not block. Create a proposed ADR under `.memory-bank/adr/` with `Status: Proposed` and `Confidence: Unconfirmed`, and note that human review is required.

## 5. Concurrent Session Safety (Locking)

To prevent split-brain issues or concurrent writes:
- Agents must check the `"concurrency_lock"` property in `.memory-bank/active-session.json`.
- If `is_locked` is `true` and the lock was acquired by a different session ID, the agent must wait or request clearance.
