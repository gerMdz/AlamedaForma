<template>
    <div>
        Login
    {{quoteText}}
    {{authorName}}
    </div>
</template>
<script>
export default {
    name: "Home",
    data() {
        var data = {
            quoteText: "The art of programming is the art of organizing complexity, of mastering multitude and avoiding its bastard chaos as effectively as possible.",
            authorName: "Dezz",
        };
        return data;
    }
}
</script>

<style scoped>
    .quoteText {
        position: relative;
        margin: auto;
        font-family: Georgia, serif;
        font-size: 18px;
        font-style: italic;
        width: 500px;
        padding: 0.25em 40px;
        line-height: 1.45;
        color: #383838;
    }
    .quoteText:before {
        display: block;
        content: "\201C";
        font-size: 80px;
        position: absolute;
        left: -20px;
        top: -20px;
        color: #7a7a7a;
    }
    .quoteText .authorName {
        color: #999999;
        font-size: 14px;
        display: block;
        margin-top: 5px;
    }
    
    .quoteText .authorName:before {
        content: "\2014 \2009";
    }
</style>