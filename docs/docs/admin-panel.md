---
id: admin-panel
title: Admin Panel
---

## Vue.JS

The Admin Panel uses Vue.JS as the frontend framework, with Tailwind as the CSS library. Although it uses Vue.JS, the admin panel is not a Single Page Application: it's served by Inertia.js.

## Icons

The project uses the Line Awesome icons. Search the icon you want to use at `https://icons8.com/line-awesome` and pass it to the `CBIcon` Vue component:

```vue
<template>
    <div>
        <CBIcon name="edit" />
    </div>
</template>

<script>
    import { CBIcon } from '../Components' // Change the path to the Components directory as needed

    export default {
        components: { CBIcon },

        // ...
    }
</script>
```

You can pass additional attributes to `CBIcon`:

```html
<CBIcon name="trash" class="text-red-500" />
```
