# Maintenance: pulling upstream updates

This is the **McMonte fork** of
[`justinhunt/moodle-tiny_snippet`](https://github.com/justinhunt/moodle-tiny_snippet)
with local customizations layered as commits on `master`. Upstream remains
the source of truth for the underlying plugin; we rebase on top of it.

## Remotes

| Name | URL | Purpose |
|---|---|---|
| `origin` | `git@github.com:McMonte/moodle-tiny_snippet.git` | Our fork — push/pull customizations here |
| `upstream` | `https://github.com/justinhunt/moodle-tiny_snippet.git` | Read-only, fetch only |

If the `upstream` remote is missing on a fresh clone, add it once:

```bash
git remote add upstream https://github.com/justinhunt/moodle-tiny_snippet.git
```

## Update procedure

```bash
cd /var/www/moodle/public/lib/editor/tiny/plugins/snippet

# 1. Pull latest upstream
git fetch upstream

# 2. Rebase our customizations on top of upstream/master
git rebase upstream/master
#    Resolve conflicts in the order git reports them. The files most
#    likely to conflict are listed at the bottom of this file.

# 3. Rebuild the AMD bundle (commit D regenerates the *.min.js files)
cd /var/www/moodle
npx grunt amd --root=public/lib/editor/tiny/plugins/snippet

# 4. Purge Moodle caches so mustache + AMD + lang changes take effect
php public/admin/cli/purge_caches.php

# 5. Test in a browser (see Verification below)

# 6. Push the rebased branch to the fork
cd public/lib/editor/tiny/plugins/snippet
git push --force-with-lease origin master
```

## Verification after rebase

Open any Moodle page with the TinyMCE editor and confirm:

- Snippets toolbar button still opens the selector modal
- Snippets appear as a vertical list grouped under headings (group names
  come from `classes/constants.php` → `GROUP_NAMES`)
- Clicking a snippet opens the options panel
- HTML in a snippet's instructions field renders as markup (e.g. `<strong>`
  shows as bold, not as literal angle brackets)
- Inserting a snippet writes the expected content into the editor

Then in Site admin → Plugins → Tiny → Snippet:

- The per-snippet field reads **"Group / Sort order"** (not "Snippet
  version")
- Editing a snippet's value to e.g. `2.5.0` moves it into group 2, sort
  position 5 after a cache purge

## Exporting a literal patch series

If you ever need a stand-alone patch file (for sharing the customizations
outside git, or for emergency re-application), generate one with:

```bash
git format-patch upstream/master..HEAD --stdout > monte-customizations.patch
```

This produces a single patch covering all four commits (A–D below) on top
of upstream.

## Our commits on top of upstream

| # | Commit | Why |
|---|---|---|
| A | Render snippet instructions as HTML | `{{instructions}}` → `{{{instructions}}}` so admin-authored HTML renders |
| B | Group and sort snippets in selector by version field | Repurpose `snippetversion_X` as `GROUP.SORT.ignored`; vertical-list layout with per-group headings |
| C | Lint cleanups in upstream AMD sources | Required so `grunt amd` will run; candidate to PR upstream |
| D | Rebuild AMD bundles | Regenerated `amd/build/*.min.js` from the patched `amd/src/` |

Commits C and D will get squashed away if the equivalent fixes land
upstream; commits A and B are intentional Monte-specific differentiation
and are expected to live here indefinitely.

## Files most likely to conflict during rebase

In rough order of risk:

1. `amd/src/widget_selector.js` — we added `context.groups = config.groups`
   and removed unused imports
2. `classes/plugininfo.php` — we added sort/group logic to
   `get_params_for_js()` and a new `parse_group_sort()` helper
3. `classes/settingstools.php` — we relabelled the `snippetversion_X`
   field's label and help text (setting key unchanged)
4. `amd/src/template_presets_amd.js` — we converted the whole file from
   tabs to 4-space indent (large mechanical diff)
5. `templates/widgetselector.mustache` — we restructured the loop to
   iterate groups instead of widgets directly
6. `templates/widgetoptions.mustache` — one-character change
   (`{{instructions}}` → `{{{instructions}}}`)
7. `classes/constants.php` — we added the `GROUP_NAMES` array
8. `lang/en/tiny_snippet.php` — we added two new strings
9. `styles.css` — we replaced the inline-block button rule and added
   group-title styles
