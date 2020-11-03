<style>
    body {
        background: #f4f6f8;
    }

    .loading {
        text-align: center;
        padding-top: calc((80vh - 256px) / 2);
    }

    .lds-dual-ring {
        display: inline-block;
        width: 256px;
        height: 256px;
    }

    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 238px;
        height: 238px;
        margin: 1px;
        border-radius: 50%;
        border: 20px solid #ddd;
        border-color: #ddd transparent #ddd transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }

    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>